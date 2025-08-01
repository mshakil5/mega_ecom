<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Authenticatable 
{
    use HasFactory, LogsActivity, SoftDeletes;
    use Notifiable;


    protected $fillable = [
        'id_number', 'name', 'email', 'password', 'phone', 'image', 'balance', 'vat_reg', 'address', 'company', 'contract_date', 'status', 'created_by', 'updated_by'
    ];

     protected $hidden = [
        'password',
    ];

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

    public function products()
    {
        return $this->hasManyThrough(Product::class, SupplierStock::class, 'supplier_id', 'id', 'id', 'product_id');
    }

    public function supplierStocks()
    {
        return $this->hasMany(SupplierStock::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }

    public function supplierTransaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public static function getAllsuppliersWithBalance()
    {
        $suppliers = self::withCount('orderDetails','purchase')->withSum(['supplierTransaction' => function ($query){
            $query->where('table_type', 'Purchase')->whereIn('payment_type', ['Credit'])->where('status', 0);
        }], 'at_amount')
        ->orderby('id','DESC')
        ->get();

        $suppliers->each(function ($data) {
            $data->total_decreament = $data->supplierTransaction()
                ->whereIn('table_type', ['Purchase'])->whereIn('payment_type', ['Cash', 'Bank'])
                ->where('status', 0)
                ->sum('at_amount');
        });

        return $suppliers;
    }
}
