<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class B2BProdRate extends Model
    {
        use HasFactory;
    
        protected $table = 'b2b_prod_rates';
        
        protected $fillable = [
            'product_id',
            'b2b_user_id',
            'rate',
            'sale_rate',
            'created_by',
            'updated_by',	
        ];
    
    }
?>