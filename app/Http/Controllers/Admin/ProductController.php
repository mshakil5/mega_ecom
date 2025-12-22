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
use App\Models\ProductPrice;
use App\Models\ProductReview;
use App\Models\Type;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\URL;
use App\Models\Stock;
use App\Imports\ProductUploadImport;
use Maatwebsite\Excel\Facades\Excel;
use Image;
use App\Models\ProductPositionImage;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function getProduct(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'subCategory', 'brand', 'productModel'])
                ->withCount('orderDetails', 'purchaseHistories')
                ->select('id', 'name', 'category_id', 'sub_category_id', 'brand_id', 
                        'product_model_id', 'is_featured', 'is_recent', 'is_popular', 
                        'is_trending', 'feature_image', 'product_code', 'active_status')
                ->orderBy('id', 'DESC');

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    $imageUrl = asset('images/products/' . $row->feature_image);
                    return '<img src="' . $imageUrl . '" style="width: 50px; height: 50px; object-fit: cover;">';
                })
                ->addColumn('price', function($row) {
                    $price = \App\Models\Stock::where('product_id', $row->id)
                        ->orderBy('id', 'desc')
                        ->first();
                    return number_format($price ? $price->selling_price : 0, 2);
                })
                ->addColumn('total_quantity', function($row) {
                    $totalQty = \App\Models\Stock::where('product_id', $row->id)
                        ->sum('quantity');
                    return number_format($totalQty ?: 0, 0);
                })
                ->addColumn('category', function($row) {
                    return $row->category->name ?? '-';
                })
                ->addColumn('status_switch', function($row) {
                    $checked = $row->active_status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-active" 
                            id="customSwitchActive' . $row->id . '" 
                            data-id="' . $row->id . '" ' . $checked . '>
                        <label class="custom-control-label" for="customSwitchActive' . $row->id . '"></label>
                    </div>';
                })
                ->addColumn('featured_switch', function($row) {
                    $checked = $row->is_featured == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-featured" 
                            id="customSwitch' . $row->id . '" 
                            data-id="' . $row->id . '" ' . $checked . '>
                        <label class="custom-control-label" for="customSwitch' . $row->id . '"></label>
                    </div>';
                })
                ->addColumn('recent_switch', function($row) {
                    $checked = $row->is_recent == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-recent" 
                            id="customSwitchRecent' . $row->id . '" 
                            data-id="' . $row->id . '" ' . $checked . '>
                        <label class="custom-control-label" for="customSwitchRecent' . $row->id . '"></label>
                    </div>';
                })
                ->addColumn('popular_switch', function($row) {
                    $checked = $row->is_popular == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-popular" 
                            id="customSwitchPopular' . $row->id . '" 
                            data-id="' . $row->id . '" ' . $checked . '>
                        <label class="custom-control-label" for="customSwitchPopular' . $row->id . '"></label>
                    </div>';
                })
                ->addColumn('trending_switch', function($row) {
                    $checked = $row->is_trending == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input toggle-trending" 
                            id="customSwitchTrending' . $row->id . '" 
                            data-id="' . $row->id . '" ' . $checked . '>
                        <label class="custom-control-label" for="customSwitchTrending' . $row->id . '"></label>
                    </div>';
                })
                ->addColumn('action', function($row) {
                    return '
                        <a id="viewBtn" href="' . route('product.show.admin', $row->id) . '">
                            <i class="fa fa-eye" style="color: #4CAF50; font-size:16px; margin-right: 10px;"></i>
                        </a>
                        <a href="' . route('product.reviews.show', $row->id) . '" class="reviewBtn d-none">
                            <i class="fa fa-comments" style="color: #FF5722; font-size:16px; margin-right: 10px;" title="View Reviews"></i>
                        </a>
                        <a href="' . route('product.prices.show', $row->id) . '">
                            <i class="fa fa-money d-none" style="color: #FF9800; font-size:16px; margin-right: 10px;"></i>
                        </a>
                        <a href="' . route('product.edit', $row->id) . '" id="EditBtn" rid="' . $row->id . '">
                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px; margin-right: 10px;"></i>
                        </a>
                        <a class="deleteBtn" rid="' . $row->id . '">
                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>
                        </a>
                    ';
                })
                ->rawColumns(['image', 'status_switch', 'featured_switch', 'recent_switch', 'popular_switch', 'trending_switch', 'action', 'total_quantity'])
                ->make(true);
        }

        return view('admin.product.index');
    }

    public function createProduct()
    {
        $brands = Brand::select('id', 'name')->orderby('id','DESC')->get();
        $product_models = ProductModel::select('id', 'name')->orderby('id','DESC')->get();
        $groups = Group::select('id', 'name')->orderby('id','DESC')->get();
        $units = Unit::select('id', 'name')->orderby('id','DESC')->get();
        $categories = Category::select('id', 'name')->orderby('id','DESC')->get();
        $subCategories = SubCategory::select('id', 'name', 'category_id')->orderby('id','DESC')->get();
        $sizes = Size::select('id', 'size')->orderby('id','DESC')->get();
        $types = Type::select('id', 'name')->where('status', 1)->orderby('id','DESC')->get();
        return view('admin.product.create', compact('brands', 'product_models', 'groups', 'units', 'categories', 'subCategories', 'sizes', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required',
            'price' => 'nullable|numeric',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,id',
            'sku' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'unit_id' => 'nullable|exists:units,id',
            'group_id' => 'nullable|exists:groups,id',
            'feature_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            // 'color_id' => 'nullable|array',
            // 'color_id.*' => 'exists:colors,id',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'position_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('feature_image')) {
            $image = $request->file('feature_image');
            $randomName = mt_rand(10000000, 99999999) . '.webp';
            $destination = public_path('images/products/');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            Image::make($image)->encode('webp', 50)->save($destination . $randomName);
            $imagePath = $randomName;
        }

        $slug = Str::slug($request->name);
        $count = Product::where('slug', 'like', "$slug%")->count();
        $uniqueSlug = $count ? "{$slug}-" . ($count + 1) : $slug;

        $latestProduct = Product::where('product_code', 'like', "STL-{$request->product_code}-" . date('Y') . '-%')
            ->orderBy('product_code', 'desc')
            ->first();

        $nextNumber = $latestProduct ? (intval(substr($latestProduct->product_code, -5)) + 1) : 1;
        $productCode = "STL-{$request->product_code}-" . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $product = Product::create([
            'name' => $request->name,
            'slug' => $uniqueSlug,
            'product_code' => $productCode,
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
            'is_customizable' => $request->is_customizable ? 1 : 0,
        ]);

        if($request->size_ids){
            foreach ($request->size_ids as $sizeId) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size_id' => $sizeId,
                    'created_by' => auth()->user()->id,
                ]);
            }
        }

        if (isset($request->color_id) && is_array($request->color_id)) {
            foreach ($request->color_id as $key => $colorId) {
                if (is_null($colorId)) {
                    continue;
                }
                $productColor = new ProductColor();
                $productColor->product_id = $product->id;
                $productColor->color_id = $colorId;

                if ($request->hasFile('image.' . $key)) {
                    $colorImage = $request->file('image.' . $key);
                    $randomName = mt_rand(10000000, 99999999) . '.webp';
                    $destinationPath = public_path('images/products/');

                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    Image::make($colorImage)
                        ->encode('webp', 30)
                        ->save($destinationPath . $randomName);

                    $productColor->image = '/images/products/' . $randomName;
                }

                $productColor->created_by = auth()->user()->id;
                $productColor->save();
            }
        }

        // Insert Product Position Images
        if ($request->has('position_images') && is_array($request->position_images)) {
            foreach ($request->position_images as $position => $image) {
                if ($image && $image->isValid()) {
                    $randomName = mt_rand(10000000, 99999999) . '.webp';
                    $destinationPath = public_path('images/products/position/');
                    
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    Image::make($image)
                        ->encode('webp', 30)
                        ->save($destinationPath . $randomName);

                    // Insert into product_position_images table
                    ProductPositionImage::create([
                        'product_id' => $product->id,
                        'image' => 'images/products/position/' . $randomName,
                        'position' => $position,
                    ]);
                }
            }
        }

        if ($request->has('type_id')) {
            $product->types()->sync($request->type_id);
        }

        return response()->json(['message' => 'Product created successfully!', 'product' => $product], 201);
    }

    public function productEdit($id)
    {
        $product = Product::withoutGlobalScopes()->with('colors', 'sizes', 'types', 'positionImages')->findOrFail($id);
        $brands = Brand::select('id', 'name')->orderby('id','DESC')->get();
        $product_models = ProductModel::select('id', 'name')->orderby('id','DESC')->get();
        $groups = Group::select('id', 'name')->orderby('id','DESC')->get();
        $units = Unit::select('id', 'name')->orderby('id','DESC')->get();
        $categories = Category::select('id', 'name')->orderby('id','DESC')->get();
        $subCategories = SubCategory::select('id', 'name')->orderby('id','DESC')->get();
        $sizes = Size::select('id', 'size')->orderby('id','DESC')->get();
        $colors = Color::select('id', 'color', 'color_code')->orderby('id','DESC')->get();
        $types = Type::select('id', 'name')->where('status', 1)->orderby('id','DESC')->get();
        return view('admin.product.edit', compact('product', 'brands', 'product_models', 'groups', 'units', 'categories', 'subCategories', 'sizes', 'colors', 'types'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products,product_code,' . $request->id . ',id,deleted_at,NULL',
            'price' => 'nullable|numeric',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,id',
            'sku' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'unit_id' => 'nullable|exists:units,id',
            'group_id' => 'nullable|exists:groups,id',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            // 'color_id' => 'nullable|array',
            // 'color_id.*' => 'exists:colors,id',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
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
            $randomName = mt_rand(10000000, 99999999) . '.webp';
            $destinationPath = public_path('images/products/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            Image::make($image)
                ->encode('webp', 50)
                ->save($destinationPath . $randomName);

            $product->feature_image = $randomName;
        }

        $product->name = $request->name;
        // $product->slug = Str::slug($request->name);
        // $product->product_code = $request->product_code;
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
        $product->is_customizable = $request->is_customizable ? 1 : 0;

        $product->save();

        if ($request->has('size_ids')) {
            $product->sizes()->sync($request->size_ids);
        }

        if ($request->has('type_id')) {
            $product->types()->sync($request->type_id);
        } else {
            $product->types()->sync([]);
        }

        if ($request->has('position_images') && is_array($request->position_images)) {
            foreach ($request->position_images as $position => $image) {
                if ($image && $image->isValid()) {
                    // Check if position image already exists
                    $existingPositionImage = ProductPositionImage::where('product_id', $product->id)
                        ->where('position', $position)
                        ->first();
                    
                    // Delete old image if exists
                    if ($existingPositionImage && file_exists(public_path($existingPositionImage->image))) {
                        unlink(public_path($existingPositionImage->image));
                        $existingPositionImage->delete();
                    }
                    
                    // Upload new image
                    $randomName = mt_rand(10000000, 99999999) . '.webp';
                    $destinationPath = public_path('images/products/position/');
                    
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    Image::make($image)
                        ->encode('webp', 30)
                        ->save($destinationPath . $randomName);

                    // Insert into product_position_images table
                    ProductPositionImage::create([
                        'product_id' => $product->id,
                        'image' => 'images/products/position/' . $randomName,
                        'position' => $position,
                    ]);
                }
            }
        }

        // Handle delete position images requests
        if ($request->has('delete_position_images') && is_array($request->delete_position_images)) {
            foreach ($request->delete_position_images as $position => $value) {
                if ($value == 1) {
                    $positionImage = ProductPositionImage::where('product_id', $product->id)
                        ->where('position', $position)
                        ->first();
                    
                    if ($positionImage) {
                        // Delete file from storage
                        if (file_exists(public_path($positionImage->image))) {
                            unlink(public_path($positionImage->image));
                        }
                        // Delete record from database
                        $positionImage->delete();
                    }
                }
            }
        }

        if ($request->has('type_id')) {
            $product->types()->sync($request->type_id);
        }

        if ($request->has('color_id')) {
            $existingColors = $product->colors;
            $previousColorIds = $request->input('previous_color_ids', []);
            $updatedColorIds = $request->input('color_id', []);
        
            foreach ($previousColorIds as $key => $previousColorId) {
                $productColor = $existingColors->where('id', $previousColorId)->first();
        
                if ($productColor) {
                    $newColorId = $updatedColorIds[$key] ?? null;
        
                    if ($newColorId && $newColorId != $productColor->color_id) {
                        $productColor->color_id = $newColorId;
                    }
        
                    if ($request->hasFile('image.' . $key)) {
                        if ($productColor->image && file_exists(public_path($productColor->image))) {
                            unlink(public_path($productColor->image));
                        }
        
                        $colorImage = $request->file('image.' . $key);
                        $randomName = mt_rand(10000000, 99999999) . '.webp';
                        $destinationPath = public_path('images/products/');

                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }

                        Image::make($colorImage)
                            ->encode('webp', 30)
                            ->save($destinationPath . $randomName);

                        $productColor->image = '/images/products/' . $randomName;
                    }

                    $productColor->save();
                }
            }

            foreach ($updatedColorIds as $key => $newColorId) {
                if (!in_array($newColorId, $previousColorIds)) {
                    $existingColor = $existingColors->where('color_id', $newColorId)->first();
        
                    if (!$existingColor) {
                        $productColor = new ProductColor();
                        $productColor->product_id = $product->id;
                        $productColor->color_id = $newColorId;
        
                        if ($request->hasFile('image.' . $key)) {
                            $colorImage = $request->file('image.' . $key);
                            $randomName = mt_rand(10000000, 99999999) . '.webp';
                            $destinationPath = public_path('images/products/');

                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }

                            Image::make($colorImage)
                                ->encode('webp', 30)
                                ->save($destinationPath . $randomName);

                            $productColor->image = '/images/products/' . $randomName;
                        }
        
                        $productColor->created_by = auth()->user()->id;
                        $productColor->save();
                    }
                }
            }

            foreach ($existingColors as $productColor) {
                if (!in_array($productColor->id, $previousColorIds)) {
                    if ($productColor->image && file_exists(public_path($productColor->image))) {
                        unlink(public_path($productColor->image));
                    }

                    $productColor->delete();
                }
            }
        }

        return response()->json(['message' => 'Product updated successfully!', 'product' => $product], 200);
    }

    public function productDelete(Request $request)
    {
        $id = $request->input('id');
        
        $product = Product::withoutGlobalScopes()->find($request->id);
    
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

    public function toggleActive(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'active_status' => 'required|boolean',
        ]);

        $product = Product::find($request->id);
        $product->active_status = $request->active_status;
        $product->save();

        return response()->json(['message' => 'Active status updated successfully!']);
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

    public function toggleCustomizable(Request $r)
    {
        Product::whereId($r->id)->update(['is_customizable'=>$r->is_customizable]);
        return response()->json(['ok'=>1]);
    }

    public function showProductDetails($id)
    {
        $currency = CompanyDetails::value('currency');
        $product = Product::with([
            'colors.color', 
            'sizes', 
            'category', 
            'subCategory', 
            'brand', 
            'productModel', 
            'group', 
            'unit',
            'types', // Added types relationship
            'positionImages' // Added position images relationship
        ])->findOrFail($id);
        
        return view('admin.product.details', compact('product', 'currency'));
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

    public function showProductPrices($productId)
    {
        $product = Product::findOrFail($productId);
        $prices = $product->prices;
        return view('admin.product.prices', compact('product', 'prices'));
    }

    public function storePrice(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_quantity' => 'required|integer',
            'max_quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $data = new ProductPrice;
        $data->product_id = $request->product_id;
        $data->min_quantity = $request->min_quantity;
        $data->max_quantity = $request->max_quantity;
        $data->price = $request->price;
        $data->status = $request->status ?? 1;
        $data->created_by = auth()->id();

        if ($data->save()) {
            return response()->json(['status' => 300, 'message' => 'Price created successfully.']);
        } else {
            return response()->json(['status' => 303, 'message' => 'Server Error!']);
        }
    }

    public function priceEdit($id)
    {
        $price = ProductPrice::findOrFail($id);
        return response()->json($price);
    }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_quantity' => 'required|integer',
            'max_quantity' => 'required|integer',
            'price' => 'required|numeric',
            'priceId' => 'required|exists:product_prices,id',
        ]);

        $price = ProductPrice::find($request->priceId);
        $price->min_quantity = $request->min_quantity;
        $price->max_quantity = $request->max_quantity;
        $price->price = $request->price;
        $price->status = $request->status ?? 1;
        $price->updated_by = auth()->id();

        if ($price->save()) {
            return response()->json(['status' => 300, 'message' => 'Price updated successfully.']);
        } else {
            return response()->json(['status' => 303, 'message' => 'Failed to update price.']);
        }
    }

    public function deletePrice($id)
    {
        $price = ProductPrice::find($id);
        
        if (!$price) {
            return response()->json(['success' => false, 'message' => 'Price not found.'], 404);
        }

        if ($price->delete()) {
            return response()->json(['success' => true, 'message' => 'Price deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete price.'], 500);
        }
    }

    public function updatePriceStatus(Request $request)
    {
        $price = ProductPrice::find($request->price_id);
        if (!$price) {
            return response()->json(['status' => 404, 'message' => 'Price not found']);
        }

        $price->status = $request->status;
        $price->save();

        return response()->json(['status' => 200, 'message' => 'Price status updated successfully']);
    }

    public function productReviews($productId)
    {
        $product = Product::with('reviews')->findOrFail($productId);
        return view('admin.product.reviews', compact('product'));
    }

    public function changeReviewStatus(Request $request)
    {
        $review = ProductReview::findOrFail($request->review_id);
        $review->is_approved = $request->is_approved;
        $review->updated_by = auth()->id();
        $review->save();

        return response()->json(['success' => true]);
    }

    public function exportToExcel()
    {
        $products = Product::with(['category', 'subCategory', 'types'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Sl');
        $sheet->setCellValue('B1', 'Code');
        $sheet->setCellValue('C1', 'Name');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Category');
        $sheet->setCellValue('F1', 'Sub-category');
        $sheet->setCellValue('G1', 'Types');

        $row = 2;
        $sl = 1;

        foreach ($products as $product) {
            $types = $product->types->pluck('name')->implode(', ');
            $sellingPrice = Stock::where('product_id', $product->id)->orderByDesc('id')->value('selling_price');

            $sheet->setCellValue('A' . $row, $sl++);
            $sheet->setCellValue('B' . $row, $product->product_code);
            $sheet->setCellValue('C' . $row, $product->name);
            $sheet->setCellValue('D' . $row, $sellingPrice);
            $sheet->setCellValue('E' . $row, $product->category->name ?? '');
            $sheet->setCellValue('F' . $row, $product->subCategory->name ?? '');
            $sheet->setCellValue('G' . $row, $types);

            $row++;
        }
        
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'products_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }

    public function uploadProduct()
    {
        return view('admin.product.upload');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Name', 'Season(System will make its code)', 'Category', 'Sub-category', 'Types (comma separated)', 'Short Description', 'Long Description', 'Image']
        ], null, 'A1');

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'product_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }

    public function uploadProductStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new ProductUploadImport, $request->file('file'));

        return back()->with('success', 'Products imported successfully!');
    }

    public function getColors()
    {
        $colors = Color::select('id', 'color', 'color_code')->where('status', 1)->orderBy('id', 'DESC')->get();
        return response()->json($colors);
    }

}
