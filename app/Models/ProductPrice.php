<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPrice extends Model
{
    // REMOVE THIS LINE if you don't want soft deletes:
    // use SoftDeletes;
    
    protected $table = 'product_prices';
    
    protected $fillable = [
        'product_id',
        'category',
        'min_quantity',
        'max_quantity',
        'discount_percent',
        'price',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean',
        'discount_percent' => 'integer',
        'price' => 'float'
    ];

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}