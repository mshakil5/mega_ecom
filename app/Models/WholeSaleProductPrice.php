<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholeSaleProductPrice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function wholeSaleProduct()
    {
        return $this->belongsTo(WholeSaleProduct::class);
    }
}