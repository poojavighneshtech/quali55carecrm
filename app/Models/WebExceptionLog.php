<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class WebExceptionLog extends Model
    {
        use HasFactory;
    
        protected $table = 'web_exception_log';
        
        protected $fillable = [
            'id',
            'function',
            'controller',
            'exception',
            'exception_time',
            'user'
        ];
    
    }
?>