<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class opening_closing_balances extends Model
    {
        use HasFactory;
    
        protected $table = 'opening_closing_balances';
        
        protected $fillable = [
            "id",
            "date",
            "opening_balance_ptcash",
            "closing_balance_ptcash",
            "opening_balance_cust_cash",
            "closing_balance_cust_cash",
            "locking_state",
            'created_at',
            'created_by',
            'updated_at',
            'updated_by'
        ];
    
    }
?>