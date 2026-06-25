<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NursingCareLead extends Model
{
    use HasFactory;

    protected $table = 'nursing_care';

    protected $fillable = [
        'id',
        'customer_name',
        'patient_name',
        'contact_no',
        'alt_contact_no',
        'address_line_1',
        'address_line_2',
        'landmark',
        'area',
        'city',
        'state',
        'pincode',
        'patient_conditions',
        'duty_type',
        'duty_hours',
        'service_type',
        'gender',
        'start_date',
        'stop_date',
        'service_rate',
        'lead_owner',
        'lead_source',
        'referred_by',
        'remark',
        'status',
        'lead_platform',
        'created_by',
        'updated_by'
    ];
}
