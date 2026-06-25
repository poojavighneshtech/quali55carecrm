<?php

namespace App\Models\Quote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteCustomer extends Model
{
    use HasFactory;
    protected $table = 'quote_customer';
    protected $fillable = [
        'id',
        'customer_name',
        'contact_no',
        'gender',
        'address_line_1',
        'address_line_2',
        'area',
        'landmark',
        'city',
        'pincode',
        'state',
        'country',
        'quote_date',
        'quote_id',
        'status',
        'transport_amt',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}
