<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SampleProductAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sample_product_id',
        'wholesaler_id',
        'quantity',
        'assignment_date',
        'created_by',
    ];

    protected $dates = [
        'assignment_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the sample product
     */
    public function sampleProduct()
    {
        return $this->belongsTo(SampleProduct::class);
    }

    /**
     * Get the wholesaler (User)
     */
    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id');
    }

    /**
     * Get the user who created this assignment
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}