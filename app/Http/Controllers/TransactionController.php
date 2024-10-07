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
            'supplier_id' => 'required',
            'amount' => 'required',
            'note' => 'nullable',
        ]);

        $transaction = new SupplierTransaction();
        $transaction->supplier_id = $request->supplier_id;
        $transaction->amount = $request->amount;
        $transaction->note = $request->note;
        $transaction->date = date('Y-m-d');
        $transaction->save();

        $supplier = Supplier::findOrFail($request->supplier_id);
        $supplier->balance += $request->amount; 
        $supplier->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Payment processed successfully!',
        ]);
    }
}
