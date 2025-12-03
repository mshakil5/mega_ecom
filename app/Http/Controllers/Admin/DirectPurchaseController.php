<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Color;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\DB;
use App\Models\Size;
use App\Models\SystemLose;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class DirectPurchaseController extends Controller
{
    public function purchase()
    {
        $products = Product::with(['stock', 'types:id,name'])->orderBy('id','DESC')->select('id', 'name', 'price', 'product_code')->where('active_status', 1)->get();
        $suppliers = Supplier::where('status', 1)->select('id', 'name')->orderby('id','DESC')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $expenses = ChartOfAccount::where('account_head', 'Expenses')->where('sub_account_head', 'Cost Of Good Sold')->select('id', 'account_name')->get();

        return view('admin.stock.direct_purchase', compact('products', 'suppliers', 'colors', 'sizes','warehouses','expenses'));
    }

    public function stockStore(Request $request)
    {
        $validated = $request->validate([
            'season'                   => 'required|string',
            'supplier_id'              => 'required|exists:suppliers,id',
            'purchase_date'            => 'required|date',
            'total_amount'             => 'required|numeric',
            'discount'                 => 'nullable|numeric',
            'total_vat_amount'         => 'required|numeric',
            'net_amount'               => 'required|numeric',
            'due_amount'               => 'required|numeric',
            'cash_payment'             => 'nullable|numeric',
            'bank_payment'             => 'nullable|numeric',
            'warehouse_id'             => 'nullable|exists:warehouses,id',

            'products'                             => 'required|array',
            'products.*.product_id'                => 'required|exists:products,id',
            'products.*.quantity'                  => 'required|numeric|min:1',
            'products.*.product_size'              => 'nullable|string',
            'products.*.product_color'             => 'nullable|string',
            'products.*.type_id'                   => 'nullable|exists:types,id',
            'products.*.unit_price'                => 'required|numeric',
            'products.*.vat_percent'               => 'nullable|numeric',
            'products.*.vat_amount'                => 'nullable|numeric',
            'products.*.total_price_with_vat'      => 'required|numeric',
            'products.*.zip'                       => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            // Generate Invoice
            $prefix = 'INV-' . date('ym') . '-';

            // Find the last invoice that matches the prefix
            $lastInvoice = Purchase::where('invoice', 'like', "%{$prefix}%")
                ->orderBy('id', 'desc')
                ->value('invoice');

            $lastSeq = 0;

            if ($lastInvoice) {
                // Extract the sequence number from the invoice
                if (preg_match("/{$prefix}(\d{4})/", $lastInvoice, $matches)) {
                    $lastSeq = (int) $matches[1];
                }
            }

            $nextSeq = $lastSeq + 1;
            $invoicePrefix = "STL-{$validated['season']}-" . $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // Ensure unique invoice ID
            while (Purchase::where('invoice', $invoicePrefix)->exists()) {
                $nextSeq++;
                $invoicePrefix = "STL-{$validated['season']}-" . $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            }

            

            /** ---------------------------
             *  Create Purchase Record
             * --------------------------*/
            $purchase = Purchase::create([
                'invoice'          => $invoicePrefix,
                'supplier_id'      => $validated['supplier_id'],
                'purchase_date'    => $validated['purchase_date'],
                'purchase_type'    => $request->purchase_type,
                'total_amount'     => $validated['total_amount'],
                'discount'         => $validated['discount'],
                'total_vat_amount' => $validated['total_vat_amount'],
                'net_amount'       => $validated['net_amount'],
                'paid_amount'      => ($request->cash_payment ?? 0) + ($request->bank_payment ?? 0),
                'other_cost'       => ($request->total_additional_cost ?? 0),
                'due_amount'       => $validated['net_amount'] - (($request->cash_payment ?? 0) + ($request->bank_payment ?? 0)),
                'status'           => $request->warehouse_id ? 4 : 1,
                'created_by'       => Auth::id(),
            ]);

            /** ---------------------------
             *  Insert Purchase Items
             * --------------------------*/
            foreach ($validated['products'] as $item) {

                $vatPerUnit = ($item['unit_price'] * ($item['vat_percent'] ?? 0)) / 100;
                $totalVat   = $vatPerUnit * $item['quantity'];
                $totalPrice = $item['unit_price'] * $item['quantity'];

                    PurchaseHistory::create([
                        'purchase_id'               => $purchase->id,
                        'product_id'                => $item['product_id'],
                        'type_id'                   => $item['type_id'] ?? null,
                        'quantity'                  => $item['quantity'],
                        'product_size'              => $item['product_size'],
                        'product_color'             => $item['product_color'],
                        'purchase_price'            => $item['unit_price'],
                        'vat_percent'               => $item['vat_percent'] ?? 0,
                        'vat_amount_per_unit'       => $vatPerUnit,
                        'total_vat'                 => $totalVat,
                        'total_amount'              => $totalPrice,
                        'total_amount_with_vat'     => $totalPrice + $totalVat,
                        'remaining_product_quantity'=> $request->warehouse_id ? 0 : $item['quantity'],
                        'transferred_product_quantity'=> $request->warehouse_id ? $item['quantity'] : 0,
                        'created_by'                => Auth::id(),
                    ]);

                // Update product latest price
                Product::where('id', $item['product_id'])
                    ->update(['price' => $item['unit_price']]);

                    if (!empty($item['missing_quantity']) && $item['missing_quantity'] > 0) {
                        SystemLose::create([
                            'product_id' => $item['product_id'],
                            'purchase_id' => $purchase->id,
                            'warehouse_id' => $request->warehouse_id,
                            'quantity' => $item['missing_quantity'],
                            'size' => $item['size'],
                            'color' => $item['color'],
                            'type_id' => $item['type_id'] ?? null,
                            'reason' => 'Damaged from purchase',
                            'created_by' => auth()->id()
                        ]);
                    }
            }

            /** ---------------------------
             *  Supplier Transaction - Main
             * --------------------------*/
            $mainTransaction = Transaction::create([
                'date'           => $validated['purchase_date'],
                'supplier_id'    => $validated['supplier_id'],
                'purchase_id'    => $purchase->id,
                'table_type'     => 'Purchase',
                'description'    => 'Purchase',
                'payment_type'   => 'Credit',
                'transaction_type'=> 'Due',
                'amount'         => $validated['total_amount'],
                'vat_amount'     => $validated['total_vat_amount'],
                'discount'       => $validated['discount'] ?? 0,
                'at_amount'      => $validated['net_amount'],
                'created_by'      => auth()->id(),
            ]);

            $mainTransaction->update([
                'tran_id' => 'SL' . date('ymd') . str_pad($mainTransaction->id, 4, '0', STR_PAD_LEFT)
            ]);

            /** ---------------------------
             *  Cash Payment
             * --------------------------*/
            if ($request->cash_payment > 0) {
                $this->storePaymentTransaction(
                    $validated['purchase_date'],
                    $validated['supplier_id'],
                    $purchase->id,
                    'Cash',
                    $request->cash_payment
                );
            }

            /** ---------------------------
             *  Bank Payment
             * --------------------------*/
            if ($request->bank_payment > 0) {
                $this->storePaymentTransaction(
                    $validated['purchase_date'],
                    $validated['supplier_id'],
                    $purchase->id,
                    'Bank',
                    $request->bank_payment
                );
            }


            $expenses = $request->input('expenses');

            foreach ($expenses as $expense) {
                $transaction = new Transaction();
                $transaction->date = $validated['purchase_date'];
                $transaction->table_type = 'Expenses';
                $transaction->purchase_id = $purchase->id;
                $transaction->supplier_id = $validated['supplier_id'];
                $transaction->amount = $expense['amount'];
                $transaction->at_amount = $expense['amount'];
                $transaction->payment_type = $expense['payment_type'];
                $transaction->chart_of_account_id = $expense['expense_id'];
                $transaction->expense_id = $expense['expense_id'];
                $transaction->description = $expense['description'];
                $transaction->note = $expense['note'];
                $transaction->transaction_type = 'Current';
                $transaction->created_by = auth()->id();
                $transaction->save();
                $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase saved successfully!',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

        /**
     * Store Cash/Bank Payment Transaction
     */
    private function storePaymentTransaction($date, $supplierId, $purchaseId, $paymentType, $amount)
    {
        $trx = Transaction::create([
            'date'            => $date,
            'supplier_id'     => $supplierId,
            'purchase_id'     => $purchaseId,
            'table_type'      => 'Purchase',
            'description'     => 'Purchase Payment',
            'payment_type'    => $paymentType,
            'transaction_type'=> 'Current',
            'amount'          => $amount,
            'at_amount'       => $amount,
            'created_by'      => auth()->id(),
        ]);

        $trx->update([
            'tran_id' => 'SL' . date('ymd') . str_pad($trx->id, 4, '0', STR_PAD_LEFT)
        ]);
    }

}
