<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class UserDetails extends Model
    {
        use HasFactory;
    
        protected $table = 'user_details';
        
        protected $fillable = [
            'id',
            'user_id',
            'name',
            'contact_no',
            'secondary_contact_no',
            'whats_app_1',
            'whats_app_2',
            'addr_line_1',
            'addr_line_2',
            'landmark',
            'area',
            'city',
            'state',
            'country',
            'pincode',
            'email',
            'second_email',
            'company_type',
            'gst_no',
            'certificates',
            'profile_img',
            'flag',
            'type',
            'forgot_pass_req',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by'
        ];
    
    }
?>