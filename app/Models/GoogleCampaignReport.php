<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleCampaignReport extends Model
{
    use HasFactory;
    protected $table = 'google_campain_report';
    protected $fillable = [
        
        'id',
        'campaign',
        'date',
        'campaign_state',
        'campaign_type',
        'budget',
        'currency_code',
        'clicks',
        'impr',
        'ctr',
        'avg_cpc',
        'cost',
        'conversions',
        'total_rate',
        'view_through_conv',
        'cost_conv',
        'conv_rate',
        'city',
        'campaignid',
        'created_at',
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

?>