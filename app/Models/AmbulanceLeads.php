<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbulanceLeads extends Model
{
    use HasFactory;
    
    protected $table = 'amb_user_data';
    
    protected $fillable = [
        'patient_name',
        'customer_name',
        'customer_type',
        'contact_no',
        'date',
        'waiting_time',
        'price',
        'service_from',
        'service_to',
        'pickup_line_1',
        'pickup_line_2',
        'pickup_landmark',
        'pickup_area',
        'pickup_location',
        'pickup_city',
        'pickup_pincode',
        'pickup_email',
        'pickup_state',
        'pickup_country',
        'drop_line_1',
        'drop_line_2',
        'drop_landmark',
        'drop_area',
        'drop_location',
        'drop_city', 
        'drop_pincode',
        'drop_email',
        'drop_state',
        'drop_country',
        'ambulance_type',
        'lead_platform',
        'lead_source',
        'reffered_by',
        'status',
        'comment',
        'created_by'
    ];

}
