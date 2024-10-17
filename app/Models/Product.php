<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
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
        return $this->hasOne(Stock::class);
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

    
}
