<?php

namespace App\Models\Quote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteProducts extends Model
{
    use HasFactory;
    protected $table = 'quote_products';
    protected $fillable = [
        'customer_id',
        'product_id',
        'purchase_type',
        'rent',
        'sale',
        'rate',
        'quantity',
        'frequency',
        'frequency_type',
        'amount',
        'deleted',
        'created_by',
        'updated_at',
        'updated_by',

    ];
}
