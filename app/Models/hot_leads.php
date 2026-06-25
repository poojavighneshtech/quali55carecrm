<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hot_leads extends Model
{
    use HasFactory;
    protected $table = 'hot_leads';
    protected $fillable = [
        'hot_lead_id',
        'customer_name',
        'area',
        'city',
        'hot_leads_contact_no',
        'hot_leads_status',
        'hot_leads_desc',
        'hot_leads_reason',
        'hot_leads_lead_owner',
        'hot_leads_comment',
        'hot_leads_created_at',
        'updated_at'
    ];
}