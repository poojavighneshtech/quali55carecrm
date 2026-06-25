<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualVdrInventoryMgmt extends Model
{
    use HasFactory;
    
    protected $table = 'virtual_wh_inventory_mgmt';
    
    protected $fillable = [

        'id',
        'order_details_id',
        'vdr_prod_details_id',
        'prod_id',
        'vdr_id',
        'vdr_wh_id',
        'vir_wh_id',
        'drop_wh_id',
        'inventory_id',
        'prod_qty',
        'status',
        'remark',
        'in_time',
        'out_time',
        'del_boy',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    
    ];

}
