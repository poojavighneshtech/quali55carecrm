<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRentedProducts extends Model
{
    use HasFactory;
    
    protected $table = 'vendor_rented_products';
    
    protected $fillable = [

        'vendor_id', 
        'vendor_product_id',
        'unique_id',
        'rental_date',
        'pickup_date',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

}
