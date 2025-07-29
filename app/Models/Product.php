<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;
    
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at']);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }

    protected static function booted()
    {
        static::addGlobalScope('active_status_restriction', function (Builder $builder) {
            if (!app()->runningInConsole() && request()->is('admin/*') === false) {
                if (!Auth::check() || !Auth::user()->is_admin) {
                    $builder->where('active_status', 1);
                }
            }
        });
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function productModel()
    {
        return $this->belongsTo(ProductModel::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockhistory()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function specialOfferDetails()
    {
        return $this->hasOne(SpecialOfferDetails::class, 'product_id');
    }

    public function flashSellDetails()
    {
        return $this->hasOne(FlashSellDetails::class, 'product_id');
    }

    public function supplierStocks()
    {
        return $this->hasMany(SupplierStock::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function campaignRequestProduct()
    {
        return $this->hasOne(CampaignRequestProduct::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes', 'product_id', 'size_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'product_id');
    }

    public function purchaseHistories()
    {
        return $this->hasMany(PurchaseHistory::class, 'product_id');
    }

    public static function productSellingPriceCal()
    {
        $allproducts = self::withCount('orderDetails','purchaseHistories')
        ->select('id', 'name', 'category_id', 'sub_category_id', 'brand_id', 'product_model_id', 'is_featured', 'is_recent', 'is_popular', 'is_trending', 'feature_image', 'product_code', 'active_status')
        ->orderby('id','DESC')
        ->get();

        return $allproducts;
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function shipmentDetails()
    {
        return $this->hasMany(ShipmentDetails::class);
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, 'product_types');
    }

    public function isZip()
    {
        return $this->types->contains('slug', '__this_slug_will_never_exist__');
    }

    public function getIsZipAttribute()
    {
        return $this->isZip();
    }

    protected $appends = ['is_zip'];

    // public function getAvailableStockAttribute()
    // {
    //     return $this->stock()
    //         ->where('quantity', '>', 0)
    //         ->orderByDesc('id')
    //         ->get();
    // }

    public function getAvailableStockAttribute()
    {
        if ($this->relationLoaded('stock')) {
            return $this->stock->where('quantity', '>', 0)->values();
        }

        return collect();
    }

    public function getSellingPriceAttribute()
    {
        return $this->available_stock->first()->selling_price ?? $this->price;
    }

    public function getAvailableColorsAttribute()
    {
        return $this->available_stock->pluck('color')->unique()->values();
    }

    public function getAvailableSizesAttribute()
    {
        return $this->available_stock->pluck('size')->unique()->values();
    }

    public function getIsInStockAttribute()
    {
        if ($this->relationLoaded('stock')) {
            return $this->stock->where('quantity', '>', 0)->isNotEmpty();
        }
        
        return $this->stock()->where('quantity', '>', 0)->exists();
    }

    
}
