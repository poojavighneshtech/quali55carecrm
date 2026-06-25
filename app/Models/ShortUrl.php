<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    use HasFactory;
    
    protected $table = 'short_urls';
    
    protected $fillable = [
        'url_link_id',
        'full_url',
        'status',
        'created_at',
        'updated_at',
    ];

}
