<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadsQueryLog extends Model
{
    use HasFactory;
    protected $table = 'leads_query_log';
    protected $fillable = [        
        'user_id',
        'operation',
        'query',
        'col',
        'created_at', 
        'created_by',
        'updated_at'
    ];
}