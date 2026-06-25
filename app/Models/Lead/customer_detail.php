<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_detail extends Model
{
    use HasFactory;
    protected $table = 'customer_details';
    protected $primaryKey = 'cust_id';
    protected $fillable = [
        'cust_id',
        'customer_name',
        'address_line_1', 
        'address_line_2', 
        'landmark', 
        'area', 
        'city', 
        'pincode', 
        'state', 
        'country', 
        'cust_date', 
        'location', 
        'email_id', 
        'primary_contact_no', 
        'secondary_contact_no', 
        'prmt_address_line_1', 
        'prmt_address_line_2', 
        'cust_gender',
        'prmt_landmark', 
        'prmt_area', 
        'prmt_city', 
        'prmt_pincode', 
        'prmt_state', 
        'prmt_country', 
        'prmt_cust_date', 
        'prmt_location', 
        'prmt_email_id', 
        'prmt_secondary_contact_no',
        'addr_is_same',
        'customer_type',
        'gst_no',
        'refered_by', 
        'cust_created_at', 
        'created_at', 
        'customer_type',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    /*protected $hidden = [
        'password',
        'remember_token',
    ];*/

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /*protected $casts = [
        'email_verified_at' => 'datetime',
    ];*/

}
