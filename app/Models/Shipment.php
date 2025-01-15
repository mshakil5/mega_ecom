<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function shipmentDetails()
    {
        return $this->hasMany(ShipmentDetails::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
