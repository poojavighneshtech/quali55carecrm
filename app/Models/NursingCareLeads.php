<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NursingCareLeads extends Model
{
    use HasFactory;
    
    protected $table = 'nursing_care_leads';
    
    protected $fillable = [
        'name',
        'contact_no',
        'email_id',
        'service_required',
        'date',
        'nurses_type',
        'dutie_hr',
        'therapeutic_rqrmt',
        'price',
        'line_1',
        'line_2',
        'landmark',
        'area',
        'location',
        'pincode',
        'state',
        'country',
        'lead_platform',
        'lead_source',
        'reffered_by',
        'status',
        'comment',
        'created_date',
        'created_by'
    ];

}
