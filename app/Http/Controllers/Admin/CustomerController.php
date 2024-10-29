<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Transaction;
use App\Mail\CustomerEmail;
use Illuminate\Support\Facades\Mail;

class CustomerController extends Controller
{
    public function getCustomer()
    {
        $data = User::where('is_type', '0')
            ->with(['customerTransaction' => function ($query) {
                $query->where('table_type', 'Sales')
                    ->whereNotNull('customer_id')
                    ->whereNull('chart_of_account_id')
                    ->where('status', 0);        
            }])
            ->withSum(['customerTransaction as total_increament' => function ($query) {
                $query->where('table_type', 'Sales')
                    ->where('status', 0)
                    ->whereNotNull('customer_id')
                    ->whereNull('chart_of_account_id')
                    ->where('payment_type', 'Credit')
                    ->where('transaction_type', 'Current');
            }], 'at_amount')
            ->withSum(['customerTransaction as total_decrement' => function ($query) {
                $query->where('table_type', 'Sales')
                    ->where('status', 0)
                    ->whereNotNull('customer_id')
                    ->whereNull('chart_of_account_id')
                    ->whereIn('transaction_type', ['Return', 'Received']);
            }], 'at_amount')
            ->withCount(['orders as sales_count' => function ($query) {
                $query->where('status', '!=', 7) // exclude cancelled orders
                      ->whereIn('order_type', [0, 1]); 
            }])
            ->get();

        return view('admin.customer.index', compact('data'));
    }

    public function customerStore(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->password)){            
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Password\" field..!</b></div>"; 
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(isset($request->password) && ($request->password != $request->confirm_password)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $chkemail = User::where('email',$request->email)->first();
        if($chkemail){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This email already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $data = new User;
        $data->name = $request->name;
        $data->surname = $request->surname;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->house_number = $request->house_number;
        $data->street_name = $request->street_name;
        $data->town = $request->town;
        $data->is_type = "0";
        $data->postcode = $request->postcode;
        if(isset($request->password)){
            $data->password = Hash::make($request->password);
        }
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function customerEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = User::where($where)->get()->first();
        return response()->json($info);
    }

    public function customerUpdate(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->email)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Email \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if(empty($request->phone)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Phone \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        if(isset($request->password) && ($request->password != $request->confirm_password)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Password doesn't match.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicateemail = User::where('email',$request->email)->where('id','!=', $request->codeid)->first();
        if($duplicateemail){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This email already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = User::find($request->codeid);
        $data->name = $request->name;
        $data->surname = $request->surname;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->house_number = $request->house_number;
        $data->street_name = $request->street_name;
        $data->town = $request->town;
        $data->postcode = $request->postcode;
        if(isset($request->password)){
            $data->password = Hash::make($request->password);
        }
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }

    public function customerDelete($id)
    {

        if(User::destroy($id)){
            return response()->json(['success'=>true,'message'=>'User has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:15',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_type' => 0,
        ]);

        return response()->json($user);
    }

    public function toggleStatus(Request $request)
    {
        $customer = User::findOrFail($request->id);
        $customer->status = $request->status;
        $customer->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'customerId' => 'required',
            'paymentAmount' => 'required',
            'paymentNote' => 'nullable',
        ]);

        $transaction = new Transaction();
        $transaction->customer_id = $request->customerId;

        if ($request->hasFile('document')) {
            $uploadedFile = $request->file('document');
            $randomName = mt_rand(10000000, 99999999).'.'.$uploadedFile->getClientOriginalExtension();
            $destinationPath = 'images/customer/document/';
            $uploadedFile->move(public_path($destinationPath), $randomName);
            $transaction->document = '/' . $destinationPath . $randomName;
        }

        $transaction->amount = $request->paymentAmount;
        $transaction->at_amount = $request->paymentAmount;
        $transaction->payment_type = $request->payment_type;
        $transaction->transaction_type = "Received";
        $transaction->note = $request->paymentNote;
        $transaction->table_type = "Sales";
        $transaction->date = date('Y-m-d');
        $transaction->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Payment processed successfully!',
        ]);
    }

    public function customerTransactions($wholeSalerId)
    {
        $customer = User::whereId($wholeSalerId)->select('id', 'name')->first();
        $transactions = Transaction::where('customer_id', $wholeSalerId)
                                ->orderBy('id', 'desc')
                                ->select('id', 'date', 'note','payment_type','table_type', 'discount','at_amount','document','transaction_type')
                                ->get();

        $totalDrAmount = Transaction::where('customer_id', $wholeSalerId)->whereIn('table_type', ['Sales'])
                                        ->whereIn('payment_type', ['Credit'])
                                        ->where('transaction_type', 'Current')
                                        ->sum('at_amount');

        $totalCrAmount = Transaction::where('customer_id', $wholeSalerId)->whereIn('table_type', ['Sales'])
                                        ->whereIn('payment_type', ['Cash','Bank','Return'])
                                        ->whereIn('transaction_type', ['Return', 'Received'])
                                        ->sum('at_amount');
        $totalBalance = $totalDrAmount - $totalCrAmount;
        return view('admin.customer.transactions', compact('transactions','customer','totalBalance'));
    }

    public function updateTransaction(Request $request)
    {
        $request->validate([
            'transactionId' => 'required|integer|exists:transactions,id',
            'at_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);
    
        $transaction = Transaction::findOrFail($request->transactionId);
    
        $transaction->at_amount = $request->at_amount;
        $transaction->note = $request->note;
    
        if ($request->hasFile('document')) {
            if ($transaction->document && file_exists(public_path($transaction->document))) {
                unlink(public_path($transaction->document));
            }
    
            $uploadedFile = $request->file('document');
            $randomName = mt_rand(10000000, 99999999) . '.' . $uploadedFile->getClientOriginalExtension();
            $destinationPath = 'images/customer/document/';
            $uploadedFile->move(public_path($destinationPath), $randomName);
            
            $transaction->document = '/' . $destinationPath . $randomName;
        }

        $transaction->updated_by = auth()->user()->id;
    
        $transaction->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully!',
        ]);
    }

    public function customerEmail($id)
    {
        $customer = User::whereId($id)->select('id', 'name','email')->first();

        return view('admin.customer.email', compact('customer'));
    }

    public function sendCustomerEmail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $customer = User::find($id);

        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found.'], 404);
        }

        Mail::to($customer->email)->send(new CustomerEmail($request->subject, $request->body));
        return response()->json(['status' => 'success', 'message' => 'Email sent successfully.']);
    }

}
