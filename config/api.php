<?php

return [
    
    'image_url' => env('APP_URL') . '/assets/images/',
    'file_url' => env('APP_URL') . '/assets/files/',
    'read_apis_cache_expiry' => 1, // in seconds
    'aggregator_api' => [

        // 'login' => [

        //     'otp_expiration' => 5, #token expiration time in minutes
        //     'token_expiration' => 30, #token expiration time in days
        // ],
        'items_per_page' => 10,
        'valid_extensions' => ['jpg', 'jpeg', 'png'],
        'max_upload_size' => 2000,
        'circle_radius' => 3959,
        // 'health_tracker' => [
        //     'medicine_list_per_page' => 10,
        //     'medicine_list_monthly_per_page' => 31,
        //     'dates_per_page' => 10
        // ],
        'date_format' => [
            'input' => 'Y-m-d',
            'output' => 'Y-m-d',
            'internal' => 'Y-m-d'
        ],
        'time_format' => [
            'input' => 'H:i',
            'output' => 'H:i',
            'internal' => 'H:i:s'
        ],
        'appointments_pagination' => 10,
        'appointments_days' => 90,
        'timezone' => [
            'user' => env('DEFAULT_TIMEZONE', 'Asia/Kolkata')
        ],
        // 'bookings' => [
        //      'start_date' => \Carbon\Carbon::now()->format('Y-m-d'),
        //      'end_date' => \Carbon\Carbon::now()->addDay(90)->format('Y-m-d'),
        //      'start_time' => \Carbon\Carbon::now()->startOfDay()->format('H:i:s'),
        //      'end_time' => \Carbon\Carbon::now()->addDay(90)->endOfDay()->format('H:i:s'),
        //      'blocked_time' => \Carbon\Carbon::now('UTC')->subMinutes(15)->format('Y-m-d H:i:s')
        // ],
        // Authorization type Config
        // 'auth' => [
        //     'key' => 't', // t = type
        //     'mobile_no' => 'm', // If uid is mobile_no
        //     'id' => 'p', // If uid is patient_id
        // ]
    ],
    // 'booking_apis' => [
    //     'max_months' => 3, #number of months to add in start date if end date is not present
    // ],
    // 'his' => [
    //     'base_uri' => env('HIS_BASE_URI'),
    //     'api_key' => env('HIS_API_KEY'),
    //     'timeout' => 3.14
    // ]
    'default_origin_date' => '1970-01-01',
];
