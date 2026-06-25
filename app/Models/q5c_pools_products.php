<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class q5c_pools_products extends Model
{
    use HasFactory;

    protected $table = 'q5c_pools_products';
    
    protected $fillable = [
        'id',
        'pickup_order_id',
        'product_id',
        'virtual_pool_id',
        'vendor_id'	,
        'vendor_product_id',
        'vendor_rented_product_id',
        'created_at',
        'updated_at'	
    ];

}
