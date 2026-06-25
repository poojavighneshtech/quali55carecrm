<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $table = 'user_log';
    
    protected $fillable = [
        'ip_address',
        'username',
        'user_role'
    ];

}
