<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestCustomers extends Model
{
    use HasFactory;
    
    protected $table = 'lab_test_customers';
    
    protected $fillable = [
        'customer_name',
        'contact_no',
        'line_1',
        'line_2',
        'landmark',
        'area',
        'location',
        'city',
        'pincode',
        'email',
        'state',
        'country',
        'created_by',
        'created_at',
        'updated_at'
    ];

}
