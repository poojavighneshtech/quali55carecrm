<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenewalReminder extends Model
{
    use HasFactory;

    protected $table = 'renewal_reminder';
    
    protected $fillable = [
        'link_id',
        'link_tbl_id',
        'customer_id',
        'order_details_id',
        'due_month',
        'order_pickup_date',
        'customer_reponse',
        'cust_response_pickup_date',
        'cust_response_pickup_time',
        'cust_response_payment',
        'order_id',
        'created_at',
        'cust_updated_at',
        'admin_updated_at',
        'updated_at',
        'updated_by'
    ];

}
