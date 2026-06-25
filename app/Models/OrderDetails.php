<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    
    protected $fillable = [

        'id',
        'order_id',
        'customer_id',
        'product_id',
        'vendor_product_id',
        'vendor_product_details_id',
        'rented_product_id',
        'vendor_id',
        'vendor_warehouse_id',
        'product_brand',
        'product_batch',
        'product_qty',
        'months',
        'vendor_rent',
        'product_rent',
        'product_deposite',
        'transport',
        'sale_rental',
        'creation_date',
        'pickup_date',
        'collection_date',
        'cust_pickup_date',
        'stop_requested_date',
        'stop_requested_by',
        'unique_id',
        'product_serial_nos',
        'status',
        'pickup_status',
        'current_status',
        'stop_requested_reason',
        'upgraded',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

}
