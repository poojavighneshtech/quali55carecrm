<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    
    protected $table = 'tasks';
    
    protected $fillable = [

        'project_id',
        'task_name',
        'description',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

}
