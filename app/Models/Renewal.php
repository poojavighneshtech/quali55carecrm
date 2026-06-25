<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    use HasFactory;

    protected $table = 'renewals';
    
    protected $fillable = [
        'collection_order_id',
        'order_id',
        'order_details_id',
        'lead_id',
        'vendor_id',
        'product_id',
        'start_date',
        'end_date',
        'created_at',
        'payment_mode',
        'cash_amount',
        'online_amount',
        'online_method',
        'discount_amt',
        'total_amt',
        'status',
        'payment_status',
        'reference_id',
        'comment',
        'image_path',
        'created_at',
        'created_by',
        'updated_at'
    ];

}
