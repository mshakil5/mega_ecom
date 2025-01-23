<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function purchaseHistory()
    {
        return $this->belongsTo(PurchaseHistory::class, 'purchase_history_id');
    }

    public function systemLose()
    {
        return $this->hasOne(SystemLose::class, 'shipment_detail_id');
    }
}
