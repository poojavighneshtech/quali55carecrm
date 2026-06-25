<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class leads_log extends Model
{
    use HasFactory;

    protected $table = 'leads_log';
    protected $primaryKey = 'log_id';
    protected $fillable = [        
        'log_id',
        'log_lead_id',
        'log_lead_status',
        'log_order_id',
        'log_order_type',
        'log_order_lead_date',
        'log_date',
        'log_time',
        'updated_by'
    ];
}