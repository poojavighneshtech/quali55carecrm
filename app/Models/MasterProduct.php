<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProduct extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'id',
        'product_name',
        'product_details',
        'product_qty',
        'product_deposite',
        'product_rent',
        'product_sale_rate',
        'min_rent_percentage',
        'product_type',
        'product_img_url',
        'type'
    ];
}