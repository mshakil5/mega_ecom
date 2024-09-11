<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Str;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use App\Models\SupplierStock;

class SupplierController extends Controller
{
    public function getSupplier()
    {
        $data = Supplier::withCount('orderDetails')->orderby('id','DESC')->get();
        return view('admin.supplier.index', compact('data'));
    }

    public function supplierStore(Request $request)
    {
        // dd($request->all());
        if(empty($request->id_number)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier code \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $chkid = Supplier::where('id_number',$request->id_number)->first();
        if($chkid){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This supplier code already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(isset($request->password) && ($request->password != $request->confirm_password)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Supplier;
        $data->id_number = $request->id_number;
        $data->name = $request->name;
        $data->slug = Str::slug($request->name);
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->vat_reg = $request->vat_reg;
        $data->address = $request->address;
        $data->company = $request->company;
        $data->contract_date = $request->contract_date;
        if(isset($request->password)){
            $data->password = Hash::make($request->password);
        }
        $data->created_by = auth()->id(); 

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/supplier/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $data->image = $randomName;
        }
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function supplierEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Supplier::where($where)->get()->first();
        return response()->json($info);
    }

    public function supplierUpdate(Request $request)
    {
        if(empty($request->id_number)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier code \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Supplier name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatid = Supplier::where('id_number',$request->id_number)->where('id','!=', $request->codeid)->first();
        if($duplicatid){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This supplier code already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatename = Supplier::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This supplier name added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(isset($request->password) && ($request->password != $request->confirm_password)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

            $data = Supplier::find($request->codeid);
            $data->id_number = $request->id_number;
            $data->name = $request->name;
            $data->slug = Str::slug($request->name);
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->vat_reg = $request->vat_reg;
            $data->address = $request->address;
            $data->company = $request->company;
            $data->contract_date = $request->contract_date;
            if(isset($request->password)){
                $data->password = Hash::make($request->password);
            }
            $data->updated_by = auth()->id(); 

            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');

                if ($data->image && file_exists(public_path('images/supplier/'. $data->image))) {
                    unlink(public_path('images/supplier/'. $data->image));
                }

                $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
                $destinationPath = public_path('images/supplier/');
                $path = $uploadedFile->move($destinationPath, $randomName); 
                $data->image = $randomName;
                $data->save();
           }

          if ($data->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

    }

    public function supplierDelete($id)
    {
        $brand = Supplier::find($id);
        
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        if ($brand->image && file_exists(public_path('images/supplier/' . $brand->image))) {
            unlink(public_path('images/supplier/' . $brand->image));
        }

        if ($brand->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function supplierTransactions($supplierId)
    {
        $transactions = Transaction::where('supplier_id', $supplierId)
                                ->orderBy('id', 'desc')
                                ->select('id', 'amount', 'date', 'note')
                                ->get();
        return view('admin.supplier.transactions', compact('transactions'));
    }

    public function showStocks($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier not found.');
        }
        $data = SupplierStock::where('supplier_id', $id)->orderBy('id', 'desc')->get();
        return view('admin.supplier.stocks', compact('supplier', 'data'));
    }

    public function approveItem(Request $request)
    {
        $item = SupplierStock::find($request->id);
        if ($item) {
            $item->is_approved = $request->is_approved;
            $item->save();

            return response()->json(['message' => 'Updated successfully.']);
        }

        return response()->json(['message' => 'Item not found.'], 404);
    }

    public function showOrders($supplierId)
    {
        $supplier = Supplier::with(['orderDetails' => function($query) {
            $query->orderBy('created_at', 'DESC');
        }])->findOrFail($supplierId);
        return view('admin.supplier.orders', compact('supplier'));
    }

    public function toggleStatus(Request $request)
    {
        $supplier = Supplier::findOrFail($request->id);
        $supplier->status = $request->status;
        $supplier->save();

        return response()->json(['message' => 'Supplier status updated successfully']);
    }
}
