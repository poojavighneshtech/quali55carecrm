<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyRecord extends Model
{
    use HasFactory;
    protected $table = 'monthly_records';
    protected $fillable = [
        'month',
        'year',
        'city',
        'total_rental',
        'overdue_rent',
        'total_rent_collected',
        'total_unit_rented',
        'total_customer_served_rental',
        'new_rent_collected',
        'new_unit_rented',
        'new_customer_rental',
        'value_added_services',
        'renewal_rent_collected',
        'renewal_count_of_equipment',
        'vdr_payment',
        'vdr_payment_other_q5c',
        'rental_transportation',
        'transportation_expense',
        'total_expense',
        'google_spend',
        'no_of_clicks',
        'google_impr',
        'justdial',
        'offline_marketing',
        'sales_value',
        'purchase_value',
        'sales_customer',
        'sales_transport',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}