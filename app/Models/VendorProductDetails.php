<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorProductDetails extends Model
{
    use HasFactory;
    
    protected $table = 'vendor_product_details';
    
    protected $fillable = [

        'id',
        'vendor_products_id',
        'availability_status',
        'inventory_id',
        'inventory_type',
        'current_location',
        'additional_details',
        'warehouse_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'

    ];

}
