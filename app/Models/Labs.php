<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labs extends Model
{
    use HasFactory;
    
    protected $table = 'labs';
    
    protected $fillable = [
        'lab_name',
        'line_1',
        'line_2',
        'landmark',
        'area',
        'location',
        'city',
        'pincode',
        'lab_email',
        'state',
        'country',
        'aggreement',
        'other_certificates',
        'person1_name',
        'person1_contact',
        'person1_email',
        'other_contact_persons',
        'created_by',
        'created_at',
        'updated_at'
    ];

}
