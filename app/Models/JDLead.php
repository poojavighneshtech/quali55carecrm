<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JDLead extends Model
{
    use HasFactory;
    protected $table = 'jd_leads';
    protected $fillable = [
        'jd_leads_id',
        'leadid',
        'leadtype',
        'prefix',
        'name',
        'mobile',
        'phone',
        'email',
        'date',
        'category',
        'city',
        'area',
        'brancharea',
        'dncmobile',
        'dncphone',
        'company',
        'pincode',
        'time',
        'branchpin',
        'parentid',
        'status',
        'remark',
        'lead_owner',
        'created_at',
    ];
}