<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempCustDetails extends Model
{
    use HasFactory;
    
    protected $table = 'temp_cust_drop_location';
    
    protected $fillable = [

        'id',
        'pickup_id',
        'cust_id',
        'product_id',
        'lead_id',
        'vendor_product_id',
        'vendor_product_details_id',
        'status',
        'date',
        'created_at'
    ];

}
