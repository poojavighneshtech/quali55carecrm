<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorWarehoue extends Model
{
    use HasFactory;
    
    protected $table = 'vendor_warehouse';
    
    protected $fillable = [

        'vendor_id',
        'wh_name', 
        'wh_address',
        'wh_landmark',
        'wh_area',
        'wh_city',
        'wh_state',
        'wh_country',
        'wh_pincode',
        'wh_email',
        'wh_primary_contact_1',
        'wh_secondary_contact_2',
        'self_delivery_offering',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

}
