<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class LockUsers extends Model
    {
        use HasFactory;
    
        protected $table = 'lock_users';
        
        protected $fillable = [
            'id',
            'key_param',
            'user',            
            'created_at'
        ];
    
    }
?>