<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorProducts extends Model
{
    use HasFactory;
    
    protected $table = 'vendor_products';
    
    protected $fillable = [
        'vendor_id',
        'product_id',
        'product_quantity',
        'product_brand',
        'product_rent_approved',
        'product_rent_requested',
        'product_deposite',
        'warehouse_id',
        'status',
        'virtual_id',
        'virtual_warehouse',
        'batch',
        'comment',
        'created_at',
        'updated_at'
    ];

}
