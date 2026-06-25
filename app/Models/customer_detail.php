<?php

namespace App\Models;

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
        'location',
        'address_line_1',
        'address_line_2',
        'cust_gender',
        'area',
        'landmark',
        'city',
        'citygroup',
        'pincode',
        'state',
        'country',
        'cust_date',
        'primary_contact_no',
        'secondary_contact_no',
        'email_id',
        'prmt_address_line_1',
        'prmt_address_line_2',
        'prmt_landmark',
        'prmt_area',
        'prmt_city',
        'prmt_pincode',
        'prmt_state',
        'prmt_country',
        'prmt_email_id',
        'prmt_secondary_contact_no',
        'gst_no',
        'corporation_name',
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'account_type',
        'refered_by',
        'customer_type',
        'corporate_cust_id',
        'contact_person_1_name',
        'contact_person_1_no',
        'contact_person_2_name',
        'contact_person_2_no',
        'comment',
        'created_at',
        'cust_created_at',
        'updated_at',
        'updated_by'

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
