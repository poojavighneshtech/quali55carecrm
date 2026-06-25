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
        'solution_date',
        'repaired_by',
        'status',
        'complaint_date',
        'closed_date',
        'created_by',
        'created_by_id',
        'closed_by_id',
        'updated_at'
    ];

}
