<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class ActivityLog extends Model
    {
        use HasFactory;
    
        protected $table = 'activity_log';
        
        protected $fillable = [
            'id',
            'order_type',
            'key_id',
            'operation',
            'parameters',
            'old_value',
            'new_value',
            'updated_at',
            'updated_at'
        ];
    
    }
?>