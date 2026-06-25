<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lead extends Model
{
    use HasFactory;
    protected $table = 'leads';
    protected $fillable = [        
        'equipment_qty',
        'months',
        'web_order_id',
        'del_date',
        'sale_rental',
        'deposite',
        'deposite_total',
        'converted_at',
        'offered_rent',
        'offered_rent_total',
        'transport',
        'lead_owner',
        'comment',
        'payment_mode',
        'updated_by',
        'customer_id',
        'creation_date', 
        'converted_at',
        'patient_name', 
        'patient_age', 
        'patient_gender',
        'doctor_name', 
        'hospital_name', 
        'therapeutic_requirement', 
        'equipment_requirement', 
        'lead_source', 
        'lead_status',
	    'delivery_challan',
        'priority',
        'lead_value',
        'customer_type',
        'corp_master_id',
        'generated_from',
	'source_id',
        'patient_document_type',
        'patient_document_no',
        'patient_document_image',
        'leadtype',
        'b2bc_agent_id',
        'created_at', 
        'created_by'
    ];
}