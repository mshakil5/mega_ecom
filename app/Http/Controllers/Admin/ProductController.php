<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductModel;
use App\Models\Group;
use App\Models\Unit;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\CompanyDetails;

class ProductController extends Controller
{
    public function getProduct()
    {
        $brands = Brand::select('id', 'name')->orderby('id','DESC')->get();
        $product_models = ProductModel::select('id', 'name')->orderby('id','DESC')->get();
        $groups = Group::select('id', 'name')->orderby('id','DESC')->get();
        $units = Unit::select('id', 'name')->orderby('id','DESC')->get();
        $data = Product::select('id', 'name', 'price', 'category_id', 'sub_category_id', 'brand_id', 'product_model_id', 'group_id', 'unit_id', 'is_featured', 'is_recent', 'is_popular', 'is_trending')->orderby('id','DESC')->get();
        return view('admin.product.index', compact('data', 'brands', 'product_models', 'groups', 'units'));
    }

    public function productStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'brand_id' => 'required',
            'product_model_id' => 'required',
            'group_id' => 'required',
            'unit_id' => 'required',
            'sku' => 'required|integer',
            'is_featured' => 'nullable',
            'is_recent' => 'nullable',
            'feature_image' => 'nullable|image|max:10240',
            'images.*' => 'nullable|image|max:10240'
        ]);

         if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode(", ", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $product = new Product;
        $product->name = $request->input('name');
        $product->slug = Str::slug($request->input('name'));
        $product->short_description = $request->input('short_description', null);
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->sub_category_id = $request->input('sub_category_id');
        $product->brand_id = $request->input('brand_id');
        $product->product_model_id = $request->input('product_model_id');
        $product->group_id = $request->input('group_id');
        $product->unit_id = $request->input('unit_id');
        $product->sku = $request->input('sku');
        $product->is_featured = $request->input('is_featured', false);
        $product->is_recent = $request->input('is_recent', false);
        $product->created_by = auth()->user()->id;

        if ($request->hasFile('feature_image')) {
            $uploadedFile = $request->file('feature_image');
            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/products/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $product->feature_image = $randomName;
        }

        $product->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = mt_rand(10000000, 99999999).'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('images/products/');
                $imagePath = $destinationPath.$imageName;
                
                $image->move($destinationPath, $imageName);

                $productImage = new ProductImage;
                $productImage->product_id = $product->id;
                $productImage->image = $imageName;
                $productImage->created_by = auth()->user()->id;
                $productImage->save();
            }
        }

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Prodcut Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function productEdit($id)
    {
        $info = Product::where('id', $id)->with('category', 'brand', 'productModel', 'group', 'unit', 'images', 'subCategory')->first();
        return response()->json($info);
    }

    public function productUpdate(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'brand_id' => 'required',
            'product_model_id' => 'required',
            'group_id' => 'required',
            'unit_id' => 'required',
            'sku' => 'required|integer',
            'is_featured' => 'nullable',
            'is_recent' => 'nullable',
            'feature_image' => 'nullable|image|max:10240',
            // 'images.*' => 'nullable|image|max:10240'
        ]);

         if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode(", ", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $product = Product::find($request->codeid);

        $product->name = $request->input('name');
        $product->slug = Str::slug($request->input('name'));
        $product->short_description = $request->input('short_description', null);
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->sub_category_id = $request->input('sub_category_id');
        $product->brand_id = $request->input('brand_id');
        $product->product_model_id = $request->input('product_model_id');
        $product->group_id = $request->input('group_id');
        $product->unit_id = $request->input('unit_id');
        $product->sku = $request->input('sku');
        $product->is_featured = $request->input('is_featured', false);
        $product->is_recent = $request->input('is_recent', false);
        $product->updated_by = auth()->user()->id;
        $product->save();

        if ($request->hasFile('feature_image')) {
            $uploadedFile = $request->file('feature_image');

            if ($product->feature_image && file_exists(public_path('images/products/'. $product->feature_image))) {
                unlink(public_path('images/products/'. $product->feature_image));
            }

            $randomName = mt_rand(10000000, 99999999). '.'. $uploadedFile->getClientOriginalExtension();
            $destinationPath = public_path('images/products/');
            $path = $uploadedFile->move($destinationPath, $randomName); 
            $product->feature_image = $randomName;
            $product->save();
        }

        $currentProductImages = ProductImage::where('product_id', $product->id)->get();
        $existingImagesArray = [];

        foreach ($currentProductImages as $existingImage) {
            $existingImagesArray[] = $existingImage->image;
        }

        $imagesToDelete = [];

        if ($request->hasFile('images')) {
            $newImages = $request->file('images');

            foreach ($newImages as $newImage) {
                $uniqueImageName = mt_rand(10000000, 99999999). '.'. $newImage->getClientOriginalExtension();
                $destinationPath = public_path('images/products/');
                $newImagePath = $destinationPath. $uniqueImageName;
                $newImage->move($destinationPath, $uniqueImageName);

                $productImage = new ProductImage;
                $productImage->product_id = $product->id;
                $productImage->image = $uniqueImageName;
                $productImage->created_by = auth()->user()->id;
                $productImage->save();
            }
        }

        foreach ($existingImagesArray as $existingImageName) {
            if (!in_array($existingImageName, $request->input('images', []))) {
                $imagesToDelete[] = $existingImageName;
            }
        }

        if (!empty($imagesToDelete)) {
            ProductImage::whereIn('image', $imagesToDelete)->delete();
            foreach ($imagesToDelete as $fileName) {
                $filePath = public_path('images/products/'. $fileName);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Product Updated Successfully.</b></div>";

        return response()->json(['status' => 300, 'message' => $message]);
    }

    public function productDelete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.']);
        }

        $imagesToDelete = ProductImage::where('product_id', $id)->pluck('image');
        foreach ($imagesToDelete as $imageFilename) {
            $filePath = public_path('images/products/'.$imageFilename); 
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        if ($product->feature_image && file_exists(public_path('images/products/' . $product->feature_image))) {
            unlink(public_path('images/products/' . $product->feature_image));
        }

        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product and images deleted successfully.']);
    }

    public function toggleFeatured(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_featured' => 'required|boolean'
        ]);

        $product = Product::find($request->id);
        $product->is_featured = $request->is_featured;
        $product->save();
        return response()->json(['message' => 'Featured status updated successfully!']);
    }

    public function toggleRecent(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_recent' => 'required|boolean'
        ]);

        $product = Product::find($request->id);
        $product->is_recent = $request->is_recent;
        $product->save();
        return response()->json(['message' => 'Recent status updated successfully!']);
    }

    public function togglePopular(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_popular' => 'required|boolean'
        ]);

        $product = Product::find($request->id);
        $product->is_popular = $request->is_popular;
        $product->save();

        return response()->json(['message' => 'Popular status updated successfully!']);
    }

    public function toggleTrending(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'is_trending' => 'required|boolean'
        ]);

        $product = Product::find($request->id);
        $product->is_trending = $request->is_trending;
        $product->save();

        return response()->json(['message' => 'Trending status updated successfully!']);
    }

    public function showProductDetails($id)
    {
        $currency = CompanyDetails::value('currency');
        $product = Product::with('images')->findOrFail($id);
        return view('admin.product.details', compact('product', 'currency'));
    }

}
