<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressTable extends Model
{
    use HasFactory;
    
    protected $table = 'address_table';
    
    protected $fillable = [
        'order_id',
        'type',
        'address_line_1',
        'address_line_2',
        'location',
        'area',
        'landmark',
        'city',
        'pincode',
        'state',
        'country',
        'contact_no',
        'email',
        'created_by',
        'updated_by',
    ];

}
