<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRegister extends Model
{
    use HasFactory;
    
    protected $table = 'vendor_details';
    
    protected $fillable = [

        'registered_name', 
        'brand_name',
        'of_address',
        'of_landmark',
        'of_area',
        'of_city',
        'of_state',
        'of_country',
        'of_pincode',
        'of_email',
        'gst_certificate',
        'shop_establishment_certificate',
        'vendor_pan_card',
        'vendor_aggreement',
        'of_primary_contact_1',
        'of_secondary_contact_2',
        'warehouse_name',
        'warehouse_address',
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
        'authentication_status',
        'gst_status',
        'shop_establishment_status',
        'vendor_pan_card_status',
        'vendor_aggreement_status',
        'comment',
        'type',
        'created_at'
    ];

}
