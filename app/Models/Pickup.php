<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    use HasFactory;

    protected $table = 'pickups';
    
    protected $fillable = [
        'pickup_order_id',
        'del_order_id',
        'order_details_id',
        'lead_id',
        'vendor_id',
        'product_id',
        'pickup_date',
        'payment_mode',
        'cash_amount',
        'online_amount',
        'drop_location',
        'drop_vendor_id',
        'drop_warehouse_id',
        'drop_vendor_product_id',
        'created_at',
        'created_by'	
    ];

}
