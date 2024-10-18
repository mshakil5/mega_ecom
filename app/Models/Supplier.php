<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Supplier extends Authenticatable 
{
    use HasFactory;
    use Notifiable;


    protected $fillable = [
        'id_number', 'name', 'email', 'password', 'phone', 'image', 'balance', 'vat_reg', 'address', 'company', 'contract_date', 'status', 'created_by', 'updated_by'
    ];

     protected $hidden = [
        'password',
    ];

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
        return $this->hasMany(SupplierTransaction::class);
    }

    public static function getAllsuppliersWithBalance()
    {
        $suppliers = self::withCount('orderDetails','purchase')->withSum(['supplierTransaction' => function ($query){
            $query->where('table_type', 'Purchase')->where('status', 0);
        }], 'total_amount')
        ->orderby('id','DESC')
        ->get();

        $suppliers->each(function ($data) {
            $data->total_decreament = $data->supplierTransaction()
                ->whereIn('table_type', ['Purchase Return', 'Payment'])
                ->where('status', 0)
                ->sum('total_amount');
        });

        return $suppliers;
    }
}
