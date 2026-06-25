<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurses extends Model
{
    use HasFactory;
    protected $table = 'nurses_data';
    protected $fillable = [
        'id',
        'name',
        'primary_contact',
        'secondary_contact',
        'city',
        'profession',
        'gender',
        'comment',
        'status',
        'referrer_owner',
    ];
}