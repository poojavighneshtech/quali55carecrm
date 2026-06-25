<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    'ip_address_captcha_high' => env('IP_ADDRESS_CAPTCHA_HIGH', '150.242.206.255'),
    'ip_address_captcha_low' => env('IP_ADDRESS_CAPTCHA_LOW', '150.242.206.0'),
    'app_env'=>env('APP_ENV', 'prodweb'),
    'ceo_email'=>env('CEO_EMAIL', 'harddikkpatel@quali55care.com'),
    'cto_email'=>env('CTO_EMAIL', 'rahulbhanushali@quali55care.com'),
    'default_from_email' => env('FROM_EMAIL', 'tempmailquali@gmail.com'),
    'business_head_email'=>env('BUSINESS_HEAD_EMAIL', 'shraddha@quali55care.com'),
    'accounts_email'=>env('ACCOUNTS_EMAIL', 'accounts@quali55care.com'),
    'api_path_env'=>env('API_PATH_ENV', '/var/www/html/api'),
    'citylist'=>env('CITYLIST',['Mumbai','Pune','Delhi','Gurgaon']),
    // 'lead_source'=>env('LEAD_SOURCE',['New User Site','B2B Cust','Cust Ref','Google Ads','Harddik','Shraddha','Just Dial','Marketing','Offline','Old User Site','Others','Ref','Tawk.to','Whats App']),
    'lead_source'=>env('LEAD_SOURCE',[
        "Google Ads",
        "Web Chat",
        "Web Popup",
        "Web Order",
        "Web - Call",
        "Web - WhatsApp",
        "Wellness Forever",
        "Reference",
        "Ref",
        "Just Dial",
        "Agent",
        "Corporate Booking",
        "Returning Cust",
        "IndiaMart",
        "Other"]),
    'order_closed_reason'=>env('ORDER_CLOSED_REASON',[
        1=>"Higher rates",
        2=>"Staff misunderstandings on product clarification",
        3=>"Wrong demonstration",
        4=>"Delivery staff misbehaviour",
        5=>"Products Mismatch",
        6=>"Delivery delay",
        7=>"Patient expired",
        8=>"Patient back to hospital",
        99=>"Others",]),
    'prod_manager_no'=>env('BUSINESS_HEAD_EMAIL', '9820930915'),
    'business_head_id'=>env('BUSINESS_HEAD_ID','15'),
    'accounts_staff_contacts'=>env('ACCOUNTS_STAFF_CONTACTS',['9930047550','9890983877','9892542479','9833098243','9370738471']),
    'invoice_visibility'=>env('INVOICE_VISIBILITY',['19','134','97','21','22','14','144','15','24','26','27','150','153','155','177']),
    'ceo_id'=>env('CEO_ID','14'),
    'cron_job_mail'=>env('CRON_JOB_MAIL',['harddik@quali55care.com','shraddha@quali55care.com']),
    
    'accounts_id'=>env('ACCOUNTS_ID','21'),

    'web_lead_user'=>env('WEB_LEAD_USER','149'),
    'it_rahul'=>env('IT_RAHUL','97'),
    'it_abhishek'=>env('IT_ABHISHEK','19'),
    'it_vivek'=>env('IT_VIVEK','144'),
    'order_type'=>env('ORDER_TYPE',['Repair','Replace','Install','Shifting']),
    'misc_orders'=>env('MISC_ORDERS',['Repair','Shifting','Vendor Pickup','Exchange','Vendor Dropoff','Refill','Installation']),
    'accounts_id_array'=>env('ACCOUNTS_ID_ARRAY',['21','22','134','150','155','176']),
    'new_site_wp_no'=>env('NEW_SITE_WP_NO',['9370738471','9022972242','9833098243','9820616550']),
    'patient_documents'=>env('PATIENT_DOCUMENTS',['No Document','Adhar Card','Voting Card','Pan Card','Driving Licence','Passport']),
    'lead_cancellation'=>env('LEAD_CANCELLATION',[
        'Not Intrested',
        'Not Responding',
        'High Price',
        'Product Not Available',
        'Brand/Model Not Available',
        'Ringing',
        'Very Urgent Requirement',
        'Not Required',
        'Location Out of Coverage ',
        'Wil Confirm Later',
        'Already Ordered ',
        'Other']),
    'it_department'=>env('IT_DEPARTMENT',[19, 97]),
    'developer_contact'=>env('DEVELOPER_CONTACT','9370738471'),
    //------
        /*--New Code--*/
        'online_source'=>env('ONLINE_SOURCE',[
            "Google Ads",
            "Web Chat",
            "Web Popup",
            "Web Order",
            "Web - Call",
            "Web - WhatsApp",
            "Just Dial",
            "IndiaMart"]),
            
        'offline_source'=>env('OFFLINE_SOURCE',[
            "Wellness Forever",
            "Reference",
            "Ref",
            "Agent",
            "Corporate Booking",
            "Returning Cust",
            "Other"]),
        'developer_contacts'=>env('DEVELOPER_CONTACTS',['9833098243','9370738471']),

        'virtual_no'=>env('VIRTUAL_NO','9643503583'),
        'note_msg'=>env('NOTE_MSG','Please note that on completion of rental term if you wish to return the equipment then please Inform on or before the renewal date itself. In case of no information it will be force renewal for the full term irrespective of the utilization and full payment is applicable.'),
        'business_head_contact'=>env('BUSINESS_HEAD_CONTACT','9820930915'),

        'it_head_contact'=>env('IT_HEAD_CONTACT','9833098243'),
    //------
        
    // 'new_site_wp_no'=>env('NEW_SITE_WP_NO',['9370738471']),
    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    //'timezone' => 'UTC',
    'timezone' => 'Asia/Kolkata',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        

        /*
         * Package Service Providers...
         */

        // ***dpmpdf***

        Barryvdh\DomPDF\ServiceProvider::class,
        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        //---------laravel captcha-----//
        //LaravelCaptcha\Providers\LaravelCaptchaServiceProvider::class,

        //----mews captcha-----//
        Mews\Captcha\CaptchaServiceProvider::class,
        
        //------Excel library---//
        Maatwebsite\Excel\ExcelServiceProvider::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        /* dompdf */
        'PDF' => Barryvdh\DomPDF\Facade::class,
        //--captcha--//
        'Captcha' => Mews\Captcha\Facades\Captcha::class,
        //Excel
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
    ],

];
