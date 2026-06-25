<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestLeads extends Model
{
    use HasFactory;
    
    protected $table = 'lab_test_leads';
    
    protected $fillable = [
        'cust_id',
        'created_date',
        'visit_date',
        'visit_time',
        'test_id',
        'prescription_image',
        'lab_name',
        'blood_collection_address',
        'customer_price',
        'comments',
        'lead_platform',
        'lead_source',
        'reffered_by',
        'status',
        'comment',
        'created_by',
    ];

}
