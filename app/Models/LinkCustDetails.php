<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkCustDetails extends Model
{
    use HasFactory;

    protected $table = 'link_cust_details';
    
    protected $fillable = [
        'primary_contact_no',
        'customer_name',
        'products',
        'link_id',	
        'link_status',
        'terms_condition',
        'lead_source',
        'admin_r_link_status',
        'r_link_owner',
        'created_by',
        'created_at',	
        'updated_at'	
    ];

}
