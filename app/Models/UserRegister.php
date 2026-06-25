<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRegister extends Model
{
    use HasFactory;

    protected $table = 'user';
    
    protected $fillable = [
        'id',
        'vendor_id',
        'username',
        'password',
        'role',
        'email_id_user',
        'contact_no',
        'location_user',
        'user_city',
        'otp',
        'role_access',
        'remember_token',
        'created_at',
        'updated_at',
    ];

}
