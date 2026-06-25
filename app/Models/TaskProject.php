<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskProject extends Model
{
    use HasFactory;
    
    protected $table = 'tk_projects';
    
    protected $fillable = [
        
        'project_name',
        'project_status',
        'start_date',
        'end_date',
        'project_manager',
        'project_team_members',
        'description',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

}
