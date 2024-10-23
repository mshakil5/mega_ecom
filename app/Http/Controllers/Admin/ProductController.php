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
use App\Models\Color;
use App\Models\Size;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\CompanyDetails;
use App\Models\SubCategory;
use App\Models\ProductSize;
use App\Models\ProductColor;

class ProductController extends Controller
{
    public function getProduct()
    {
        $data = Product::orderby('id','DESC')->where('status', 1)->get();
        return view('admin.product.index', compact('data'));
    }

    public function createProduct()
    {
        $brands = Brand::select('id', 'name')->orderby('id','DESC')->get();
        $product_models = ProductModel::select('id', 'name')->orderby('id','DESC')->get();
        $groups = Group::select('id', 'name')->orderby('id','DESC')->get();
        $units = Unit::select('id', 'name')->orderby('id','DESC')->get();
        $categories = Category::select('id', 'name')->orderby('id','DESC')->get();
        $subCategories = SubCategory::select('id', 'name')->orderby('id','DESC')->get();
        $sizes = Size::select('id', 'size')->orderby('id','DESC')->get();
        $colors = Color::select('id', 'color', 'color_code')->orderby('id','DESC')->get();

        return view('admin.product.create', compact('brands', 'product_models', 'groups', 'units', 'categories', 'subCategories', 'sizes', 'colors'));
    }

    public function productStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            // 'sub_category_id' => 'required',
            // 'brand_id' => 'required',
            // 'product_model_id' => 'required',
            // 'group_id' => 'required',
            // 'unit_id' => 'required',
            // 'sku' => 'required|integer',
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

                $productImage = new ProductColor();
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
        $product = Product::with('colors', 'sizes')->findOrFail($id);

        $brands = Brand::select('id', 'name')->orderby('id','DESC')->get();
        $product_models = ProductModel::select('id', 'name')->orderby('id','DESC')->get();
        $groups = Group::select('id', 'name')->orderby('id','DESC')->get();
        $units = Unit::select('id', 'name')->orderby('id','DESC')->get();
        $categories = Category::select('id', 'name')->orderby('id','DESC')->get();
        $subCategories = SubCategory::select('id', 'name')->orderby('id','DESC')->get();
        $sizes = Size::select('id', 'size')->orderby('id','DESC')->get();
        $colors = Color::select('id', 'color', 'color_code')->orderby('id','DESC')->get();
    
        return view('admin.product.edit', compact('product', 'brands', 'product_models', 'groups', 'units', 'categories', 'subCategories', 'sizes', 'colors'));
    }

    public function productUpdate(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            // 'sub_category_id' => 'required',
            // 'brand_id' => 'required',
            // 'product_model_id' => 'required',
            // 'group_id' => 'required',
            // 'unit_id' => 'required',
            // 'sku' => 'required|integer',
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

    public function productDelete(Request $request)
    {
        $id = $request->input('id');
        
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.']);
        }
    
        $isInOrderDetails = $product->orderDetails()->exists();
        $isInPurchaseHistories = $product->purchaseHistories()->exists();
    
        if ($isInOrderDetails || $isInPurchaseHistories) {
            $product->status = 2; 
            $product->save();
            return response()->json(['success' => false, 'message' => 'Product is associated with orders or purchases. Status updated to 2.']);
        }
    
        if ($product->feature_image && file_exists(public_path('images/products/' . $product->feature_image))) {
            unlink(public_path('images/products/' . $product->feature_image));
        }
    
        foreach ($product->colors as $color) {
            if ($color->image && file_exists(public_path($color->image))) {
                unlink(public_path($color->image));
            }
            $color->delete();
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
        $product = Product::with(['colors.color', 'sizes.size', 'category', 'subCategory', 'brand', 'productModel', 'group', 'unit'])->findOrFail($id);
        return view('admin.product.details', compact('product', 'currency'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code',
            'price' => 'nullable|numeric',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,id',
            'sku' => 'nullable|string|max:255',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'unit_id' => 'nullable|exists:units,id',
            'group_id' => 'nullable|exists:groups,id',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'color_id' => 'nullable|array',
            // 'color_id.*' => 'exists:colors,id',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('feature_image')) {
            $image = $request->file('feature_image');
            $randomName = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $randomName);
            $imagePath = $randomName;
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'product_code' => $request->product_code,
            'price' => $request->price,
            'sku' => $request->sku,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->subcategory_id,
            'brand_id' => $request->brand_id,
            'product_model_id' => $request->product_model_id,
            'unit_id' => $request->unit_id,
            'group_id' => $request->group_id,
            'feature_image' => $imagePath,
            'created_by' => auth()->user()->id,
            'is_whole_sale' => $request->is_whole_sale ? 1 : 0,
            'is_featured' => $request->is_featured ? 1 : 0,
            'is_recent' => $request->is_recent ? 1 : 0,
            'is_new_arrival' => $request->is_new_arrival ? 1 : 0,
            'is_top_rated' => $request->is_top_rated ? 1 : 0,
            'is_popular' => $request->is_popular ? 1 : 0,
            'is_trending' => $request->is_trending ? 1 : 0,
        ]);

        foreach ($request->size_ids as $sizeId) {
            ProductSize::create([
                'product_id' => $product->id,
                'size_id' => $sizeId,
                'created_by' => auth()->user()->id,
            ]);
        }

        if ($request->has('color_id')) {
            foreach ($request->color_id as $key => $colorId) {
                $productColor = new ProductColor();
                $productColor->product_id = $product->id;
                $productColor->color_id = $colorId;

                if ($request->hasFile('image.' . $key)) {
                    $colorImage = $request->file('image.' . $key);
                    $randomName = mt_rand(10000000, 99999999) . '.' . $colorImage->getClientOriginalExtension();
                    $colorImage->move(public_path('images/products'), $randomName);
                    $productColor->image = '/images/products/' . $randomName;
                }

                $productColor->created_by = auth()->user()->id;
                $productColor->save();
            }
        }

        return response()->json(['message' => 'Product created successfully!', 'product' => $product], 201);
    }

    public function checkProductCode(Request $request)
    {
        $productCode = $request->product_code;
        $productId = $request->product_id;
    
        if ($productId) {
            $exists = Product::where('product_code', $productCode)
                            ->where('id', '!=', $productId)
                            ->exists();
        } else {
            $exists = Product::where('product_code', $productCode)->exists();
        }
    
        return response()->json(['exists' => $exists]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code,' . $request->id,
            'price' => 'nullable|numeric',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,id',
            'sku' => 'nullable|string|max:255',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'unit_id' => 'nullable|exists:units,id',
            'group_id' => 'nullable|exists:groups,id',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color_id' => 'nullable|array',
            'color_id.*' => 'exists:colors,id',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::find($request->id);

        if ($request->hasFile('feature_image')) {
            if ($product->feature_image) {
                $oldImagePath = public_path('images/products/' . $product->feature_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); 
                }
            }

            $image = $request->file('feature_image');
            $randomName = mt_rand(10000000, 99999999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $randomName);
            $product->feature_image = $randomName;
        }

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->product_code = $request->product_code;
        $product->price = $request->price;
        $product->sku = $request->sku;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->subcategory_id;
        $product->brand_id = $request->brand_id;
        $product->product_model_id = $request->product_model_id;
        $product->unit_id = $request->unit_id;
        $product->group_id = $request->group_id;
        $product->updated_by = auth()->user()->id;
        $product->is_whole_sale = $request->is_whole_sale ? 1 : 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->is_recent = $request->is_recent ? 1 : 0;
        $product->is_new_arrival = $request->is_new_arrival ? 1 : 0;
        $product->is_top_rated = $request->is_top_rated ? 1 : 0;
        $product->is_popular = $request->is_popular ? 1 : 0;
        $product->is_trending = $request->is_trending ? 1 : 0;

        $product->save();

        $product->sizes()->delete();

        if ($request->size_ids) {
            foreach ($request->size_ids as $sizeId) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size_id' => $sizeId,
                    'created_by' => auth()->user()->id,
                ]);
            }
        }

        if ($request->has('color_id')) {
            $existingColors = $product->colors;
    
            foreach ($existingColors as $color) {
                if ($color->image) {
                    unlink(public_path($color->image)); 
                }
            }
    
            $product->colors()->delete();
    
            $validColorIds = $request->input('color_id');
    
            foreach ($validColorIds as $key => $colorId) {
                $productColor = new ProductColor();
                $productColor->product_id = $product->id;
                $productColor->color_id = $colorId;
    
                if ($request->hasFile('image.' . $key)) {
                    $colorImage = $request->file('image.' . $key);
                    $randomName = mt_rand(10000000, 99999999) . '.' . $colorImage->getClientOriginalExtension();
                    $colorImage->move(public_path('images/products'), $randomName);
                    $productColor->image = '/images/products/' . $randomName;
                }
    
                $productColor->created_by = auth()->user()->id;
                $productColor->save();
            }
        }

        return response()->json(['message' => 'Product updated successfully!', 'product' => $product], 200);
    }


}
