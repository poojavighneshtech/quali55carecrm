<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaints extends Model
{
    use HasFactory;

    protected $table = 'complaints';
    
    protected $fillable = [
        'id',
        'generated_complaint_id',
        'image',
        'customer_id',
        'order_details_id',
        'product_id',
        'vendor_id',
        'lead_id',
        'delivered_by',
        'lead_owner',
        'lead_owner_id',
        'remarks',
        'solution',
        'status',
        'complaint_date',
        'closed_date',
        'created_by',
        'updated_at'
    ];

}
