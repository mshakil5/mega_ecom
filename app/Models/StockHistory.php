<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StockHistory extends Model
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    protected $appends = ['discounted_price'];

public function getDiscountedPriceAttribute()
{
    // Check for product-specific discount first
    $discount = Discount::where('product_id', $this->product_id)
        ->where('status', 1)
        ->first();

    if ($discount) {
        return $this->selling_price - ($this->selling_price * $discount->discount_percent / 100);
    }

    // Get product to access category/subcategory
    $product = $this->product;
    
    if (!$product) {
        \Log::info('Product not loaded for StockHistory ID: ' . $this->id);
        return $this->selling_price;
    }

    \Log::info('Product ID: ' . $product->id . ', Category ID: ' . $product->category_id . ', Sub Category ID: ' . $product->sub_category_id);

    // Fallback to subcategory discount
    if ($product->sub_category_id) {
        $discount = Discount::where('subcategory_id', $product->sub_category_id)
            ->where('status', 1)
            ->first();

        \Log::info('Subcategory Discount: ' . ($discount ? $discount->discount_percent : 'None'));

        if ($discount) {
            return $this->selling_price - ($this->selling_price * $discount->discount_percent / 100);
        }
    }

    // Fallback to category discount
    if ($product->category_id) {
        $discount = Discount::where('category_id', $product->category_id)
            ->where('status', 1)
            ->first();

        \Log::info('Category Discount: ' . ($discount ? $discount->discount_percent : 'None'));

        if ($discount) {
            return $this->selling_price - ($this->selling_price * $discount->discount_percent / 100);
        }
    }

    return $this->selling_price;
}
}
