<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierTransaction;

class TransactionController extends Controller
{
    public function pay(Request $request)
    {
        $request->validate([
            'supplierId' => 'required',
            'paymentAmount' => 'required',
            'paymentNote' => 'nullable',
        ]);

        $transaction = new SupplierTransaction();
        $transaction->supplier_id = $request->supplierId;

        if ($request->hasFile('document')) {
            $uploadedFile = $request->file('document');
            $randomName = mt_rand(10000000, 99999999).'.'.$uploadedFile->getClientOriginalExtension();
            $destinationPath = 'images/supplier/document/';
            $uploadedFile->move(public_path($destinationPath), $randomName);
            $transaction->document = '/' . $destinationPath . $randomName;
        }

        $transaction->amount = $request->paymentAmount;
        $transaction->total_amount = $request->paymentAmount;
        $transaction->payment_type = $request->payment_type;
        $transaction->note = $request->paymentNote;
        $transaction->table_type = "Payment";
        $transaction->date = date('Y-m-d');
        $transaction->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Payment processed successfully!',
        ]);
    }
}
