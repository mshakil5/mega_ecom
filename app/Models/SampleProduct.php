<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleProduct extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the warehouse that owns the SampleProduct
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get the purchase that owns the SampleProduct
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    /**
     * Get the shipment detail that owns the SampleProduct
     */
    public function shipmentDetail()
    {
        return $this->belongsTo(ShipmentDetails::class, 'shipment_detail_id');
    }

    /**
     * Get the user who created the SampleProduct
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the SampleProduct
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the order that owns the SampleProduct
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

     public function assignments()
    {
        return $this->hasMany(SampleProductAssignment::class);
    }

    public function getDistributedQuantityAttribute()
    {
        return $this->assignments()->sum('quantity') ?? 0;
    }

    public function getAvailableQuantityAttribute()
    {
        return $this->quantity - $this->distributed_quantity;
    }

    public function getHasAvailableQuantityAttribute()
    {
        return $this->available_quantity > 0;
    }

    public function getHasDistributedQuantityAttribute()
    {
        return $this->distributed_quantity > 0;
    }
}
