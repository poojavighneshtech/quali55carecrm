<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyExpense extends Model
{
    use HasFactory;

    protected $table = 'daily_expenses';
    
    protected $fillable = [
        'user_name',
        'deposite_paid',
        'transport',
        'expenses',
        'labour',
        'balance_cash',
        'exp_date',
        'cash_from_office',
        'received_cash',
        'img_url',
        'settled_by',
        'cash_received_from_customer',
        'holiday',
        'comment',
        'receipt_no',
        'created_at',
        'updated_at'
    ];

}
