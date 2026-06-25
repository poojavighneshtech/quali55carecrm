<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sale_vendor_products extends Model
{
    use HasFactory;
    
    protected $table = 'sale_vendor_products';
    
    protected $fillable = [

        'id',
        'order_id',
        'vendor_id',
        'product_id',
        'sale_price',
        'vendor_sale_price',
        'vendor_warehouse'

    ];

}
