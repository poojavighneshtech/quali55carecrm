<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class leads_log extends Model
{
    use HasFactory;
    protected $table = 'leads_log';
    protected $fillable = [        
        'log_lead_id',
        'log_lead_status',
        'log_date',
        'log_time',
        'updated_by'
    ];
}