<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function shipmentDetails()
    {
        return $this->hasMany(ShipmentDetail::class, 'purchase_history_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
