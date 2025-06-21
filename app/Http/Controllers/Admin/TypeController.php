<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Models\Type;
use Illuminate\Support\Str;

class TypeController extends Controller
{
    public function getType()
    {
        $data = Type::orderby('id','DESC')->get();
        return view('admin.type.index', compact('data'));
    }

    public function typeStore(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Type name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $chkname = Type::where('name',$request->name)->first();
        if($chkname){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This type already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new Type;
        $data->name = $request->name;
        $data->slug = Str::slug($request->name);
        $data->status = $request->status ?? 1;
        $data->created_by = auth()->id();
        
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $existing = Type::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();

        if ($existing) {
            return response()->json([
                'status' => 'exists',
                'message' => 'Type already exists',
                'data' => $existing
            ], 200);
        }

        $type = Type::create(['name' => $request->name]);

        return response()->json([
            'status' => 'success',
            'message' => 'Type added successfully',
            'data' => $type
        ], 201);
    }

    public function quickAddWithProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255'
        ]);

        $existing = Type::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();

        if ($existing) {
            return response()->json([
                'status' => 'exists',
                'message' => 'Type already exists',
                'data' => $existing
            ], 200);
        }

        $type = Type::create(['name' => $request->name]);

        $productType = new ProductType([
            'product_id' => $request->product_id,
            'type_id' => $type->id,
        ]);

        if ($productType->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Type added successfully',
                'data' => $type
            ], 201);
        }
    }

    public function typeEdit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Type::where($where)->get()->first();
        return response()->json($info);
    }

    public function typeUpdate(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Type name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $duplicatename = Type::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This type already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $type = Type::find($request->codeid);
        $type->name = $request->name;
        $type->status = $request->status;
        $type->updated_by = auth()->id();

        if ($type->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Failed to update data. Please try again.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }

    public function typeDelete($id)
    {
        $type = Type::find($id);
        
        if (!$type) {
            return response()->json(['success' => false, 'message' => 'Type not found.'], 404);
        }

        if ($type->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }
}