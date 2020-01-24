<?php

return [
    'USER_STATUS_ENUM' => [1 => 'Active', 0 => 'Inactive'],
    'GENDER_ENUM' => ['0' => 'Male', '1' => 'Female', '2' => 'Other'],
    'DOCTOR_VERIFY_ENUM' => [1 => 'Yes', 0 => 'No'],
    'WEEK_DAYS_ENUM' => [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday'
    ],

    'IMAGE_SIZE' => [
        'doctors' => [
            'width' => 480,
            'height' => 480
        ],
        'specialities' => [
            'width' => 480,
            'height' => 480
        ],
        'facilities' => [
            'width' => 480,
            'height' => 480
        ],
        'customerCares' => [
            'width' => 480,
            'height' => 480
        ],
        'frontEndDesks' => [
            'width' => 480,
            'height' => 480
        ],
        'adminUsers' => [
            'width' => 480,
            'height' => 480
        ],
        'patients' => [
            'width' => 480,
            'heigth' => 480
        ],
        'hospitals' => [
            'width' => 480,
            'height' => 480
        ],
        'pages' => [
            'width' => 480,
            'height' => 480
        ],
        'health_articles' => [
            'width' => 480,
            'height' => 480
        ],
        'healthOffers' => [
            'width' => 480,
            'height' => 480
        ],
        'starDoctorAssistant' => [
            'width' => 480,
            'height' => 480
        ],
        'callCentreAgents' => [
            'width' => 480,
            'height' => 480
        ],
        'couponCodes' => [
            'width' => 480,
            'height' => 480
        ],
        'prescription' => [
            'width' => 480,
            'heigth' => 480
        ],
        'hospitalAdmins' => [
            'width' => 480,
            'heigth' => 480
        ],
    ],

    'ROLE_PROFILE' => [
        'superAdminNDTV' => [
            'userRelation' => 'admin',
            'view' => 'admin.adminUser.profile'
        ],
        'superAdminFortisIT' => [
            'userRelation' => 'admin',
            'view' => 'admin.adminUser.profile'
        ],
        'superAdminSalesOffice' => [
            'userRelation' => 'admin',
            'view' => 'admin.adminUser.profile'
        ],
        'superAdminRegional' => [
            'userRelation' => 'admin',
            'view' => 'admin.adminUser.profile'
        ],
        'hospitalLevelAdmin' => [
            'userRelation' => 'hospitalAdmin',
            'view' => 'admin.doctorHospital.profile'
        ],
        'doctor' => [
            'userRelation' => 'doctor',
            'view' => 'admin.doctors.profile'
        ],
        'frontEndDesk' => [
            'userRelation' => 'frontEndDesk',
            'view' => 'admin.frontEndDesk.profile'
        ],
        'callCentreAgent' => [
            'userRelation' => 'callCentreAgent',
            'view' => 'admin.callCentreAgent.profile'
        ],
        'starDoctorAssistant' => [
            'userRelation' => 'starDoctorAssistant',
            'view' => 'admin.starDoctorAssistant.profile'
        ],

    ],
    'BLOOD_GROUP' => [
        null => 'unknown',
        0 => 'A+ve',
        1 => 'B+ve',
        2 => 'AB+ve',
        3 => 'O+ve',
        4 => 'O-ve',
        5 => 'A-ve',
        6 => 'B-ve',
        7 => 'AB-ve'
    ],
    'ALLERGIES_TYPE' => [
        null => 'unknown',
        0 => 'Food Allergies',
        1 => 'Seasonal Allergies',
        2 => 'Pet Allergies',
        3 => 'Drug Allergies',
        4 => 'Other',
        5 => 'NA'
    ],
    'ID_TYPE' => [
        null => 'unknown',
        0 => 'Aadhar card',
        1 => 'PAN card',
        2 => 'Election card',
        3 => 'Passport'
    ],
    'PAYMENT_MODE' => [
        0 => 'Paytm',
        1 => 'PayU',
        2 => 'Cash',
        3 => 'HDFC'
    ],
    'APP_PAYMENT_MODE' => [
        0 => 'paytm',
        1 => 'payu',
        // 2 => 'cash', // Cash payment disabled for app
        3 => 'hdfc',
    ],
    'PAYMENT_MODE_INTERNAL' => [
        'paytm' => 0,
        'payu' => 1,
        'cash' => 2,
        'hdfc' => 3,
    ],
    'PAYMENT_STATUS' => [
        0 => 'pending',
        1 => 'success',
        2 => 'failure'
    ],
    'PAYMENT_STATUS_INTERNAL' => [
        'pending' => 0,
        'success' => 1,
        'failure' => 2
    ],
    'FAMILY_RELATIONSHIP' => [
        0 => 'brother',
        1 => 'daughter',
        2 => 'father',
        3 => 'grand daughter',
        4 => 'grand father',
        5 => 'grand mother',
        6 => 'grand son',
        7 => 'mother',
        8 => 'sister',
        9 => 'son',
        10 => 'spouse'
    ],
    'PRESCRIPTION_FOLLOW_UP' => [
        1 => 'YES',
        0 => 'NO'
    ],
    'MEDICINE_TYPE' => [
        0 => 'Tablet',
        1 => 'Liquid'
    ],
    'MEDICINE_STATUS' => [
        0 => 'Inactive',
        1 => 'Active'
    ],
    'SCHEDULE_BREAK_TIME' => [
        '5' => '5 minutes',
        '10' => '10 minutes',
        '15' => '15 minutes',
        '20' => '20 minutes',
        '25' => '25 minutes',
        '30' => '30 minutes',
        '45' => '45 minutes',
        '60' => '60 minutes',
        '' => 'Custom',
    ],
    'APPOINTMENT_BOOKING' => [
        0 => 'Pending',
        1 => 'Confirmed',
        2 => 'Rescheduled',
        3 => 'Canceled',
        4 => 'Rejected'
    ],
    'APPOINTMENT_BOOKING_INTERNAL' => [
        'pending' => 0,
        'confirmed' => 1,
        'rescheduled' => 2,
        'canceled' => 3,
        'rejected' => 4
    ],
    'upload_path' => env('UPLOAD_PATH') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR,
    'api_url' => env('UPLOAD_URL') . '/api/',
    'asset_url' => env('UPLOAD_URL') . '/assets/',
    'upload_url' => env('UPLOAD_URL') . '/uploads/',
    'STATUS_ENUM' => [0 => 'Inactive', 1 => 'Active'],
    'defaultUsers' => [
        'superAdminNDTV' => [
            'email' => 'rajanv@ndtv.com',
            'password' => 'Ndtv@2017',
            'mobile_no' => 8860207000,
            'is_active' => 1,
            'is_verified' => 1
        ],
        'superAdminFortisIT' => [
            'email' => 'aankit@ndtv.com',
            'password' => 'Ndtv@2017',
            'mobile_no' => 8860207001,
            'is_active' => 1,
            'is_verified' => 1
        ],
    ],
    'admin' => [
        'logs' => [
            'items_per_page' => 20,
        ]
    ],
    'success_flag' => 200, //return success
    'data_missing' => 400, //The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat the request without modifications.
    'invalid_data' => 402, //invalid api key, incorrect username password,
    'excelReportSettings' => [
        'author' => 'admin@fortishealthcare.com',
        'company' => 'Fortis Health Care',
        'description' => 'Fortis Health Care Report',
        'excelFormat' => 'xls',
        'fileOption' => 'download',
        'fileName' => 'document_export',
        'sheetName' => 'Sheet1',
        'password_protected' => false,
        'password' => 'master',
        'upload_path' => '/uploads/reports',
        'duration_of_last' => '-3 months'
    ],
    'PDF_MASTER_PASSWORD' => '123456',
    'audit_logs' => [
        'date' => [
            'display_timezone' => 'Asia/Kolkata',
            'display_format' => 'd/m/Y h:i:s A',
        ],
    ],
    'sms_logs' => [
        'date' => [
            'display_timezone' => 'Asia/Kolkata',
            'display_format' => 'd/m/Y h:i:s A',
        ],
    ],
    'homePage' => [
        'superAdminNDTV' => '/home',
        'superAdminFortisIT' => '/admin/bookings',
        'superAdminSalesOffice' => 'admin/patients',
        'superAdminRegional' => '/admin/patients',
        'hospitalLevelAdmin' => '/admin/patients',
        'doctor' => '/admin/patients',
        'frontEndDesk' => '/admin/patients',
        'callCentreAgent' => '/admin/patients',
        'starDoctorAssistant' => '/admin/patients',
    ],
    'email_params' => [
        'content_type' => ['text' => 'text/plain', 'html' => 'text/html'],
        'email_type' => ['registration' => 'Registration', 'appointment_booking' => 'Appointment Booking', 'booking_cancel' => 'Booking Cancel'],
        'date' => [
            'display_timezone' => 'Asia/Kolkata',
            'display_format' => 'd/m/Y h:i:s A',
        ],
    ],

    'appointment_cancellation_reasons' => [
        0 => 'I will not be able to come on time for appointment',
        1 => 'I want to visit a different doctor at a different time',
        2 => 'I have already booked an appointment with a doctor, in some other hospital',
        3 => 'I need guidance from the staff, as to which speciality/doctor I should meet',
        4 => 'Other Reason', # Temporary Fix
        9 => 'Other Reason'
    ],

    'booking_source' => [
        0 => 'Unknown',
        1 => 'Agent Portal',
        2 => 'Customer Portal',
        3 => 'Android App',
        4 => 'iOS App',
        // Aggreagtor Source only onwords, format: Aggregator (Camel cased without space) Check source.php for aggreagtor types.
        5 => 'Aggregator (3rdParty)',
        // 6 => 'Aggregator (Practo)',
        // 7 => 'Aggregator (triptotreat)',
        // 8 => 'Aggregator (credihealth)',
        // 9 => 'Aggregator (lybrate)',
        // 10 => 'Aggregator (vidalhealth)',
        // 11 => 'Aggregator (Alexa)',
        // 12 => 'Aggregator (HDFCergo)',
        // 13 => 'Aggregator (Praktice)'
    ],

    'booking_source_internal' => [
        'unknown' => 0,
        'agent_portal' => 1,
        'customer_portal' => 2,
        'android_app' => 3,
        'ios_app' => 4,
        // Aggreagtor Source only onwords, format: aggregator_<lowercased name without space>
        'aggregator_3rdparty' => 5,
        // 'aggregator_practo' => 6,
        // 'aggregator_triptotreat' => 7,
        // 'aggregator_credihealth' => 8,
        // 'aggregator_lybrate' => 9,
        // 'aggregator_vidalhealth' => 10,
        // 'aggregator_alexa' => 11,
        // 'aggregator_hdfcergo' => 12,
        // 'aggregator_praktice' => 13,
    ],

    'PRICING_SCHEMA' => [0 => 'Normal', 1 => 'BPL'],
    'PATIENT_TYPE' => [0 => 'Normal', 1 => 'VIP'],
    'TITLE' => [0 => 'Dr.', 1 => "Mr.", 2 => "Ms.", 3 => "Mrs."],
    'QUALIFICATION' => ['MBBS' => 'M.B.B.S', 'MD' => 'M.D', 'BMBS' => 'B.M.B.S', 'BDS' => 'B.D.S', 'DMR' => 'D.M.R'],
    'DEFAULT_APPOINTMENT_DURATION' => 15, //Minutes
    'PER_DAY_SESSION_LIMIT' => 4,
    'SCHEDULE_TYPE' => [0 => 'Recurring', 1 => 'Fixed'],

    'date_format' => [
        'input' => 'd/m/Y',
        'output' => 'd/m/Y',
        'internal' => 'Y-m-d'
    ],
    'IMAGE_EXTENSION' => 'jpeg,jpg,png',
    'IMAGE_TYPE' => 'image/jpeg,image/png,image/jpg',
    'PDF_EXTENSION' => 'pdf',
    'PDF_TYPE' => 'application/pdf',
    'MAX_FILE_SIZE' => '2097152',
    'DISCOUNT_TYPE' => [0 => 'Flat', 1 => 'Percentage'],
    'CODE_GENERATE_OPTION' => [0 => 'Auto Generate', 1 => 'Custom'],

    'CODE_PREFIX' => [
        'AdminUser' => 'SA',
        'Doctor' => 'D',
        'FrontEndDesk' => 'DESK',
        'CallCentreAgent' => 'AGENT',
        'StarDoctorAssistant' => 'ASSIST',
        'Hospital' => 'H',
        'Speciality' => 'S',
        'Booking' => 'BA',
        'MasterPatient' => 'MH',
        'BookingRequest' => 'AR',
        'HospitalAdmin' => 'AD',
        'Transaction' => 'PAY'
    ],

    'BOOKING' => [
        'STATUS' => [
            0 => 'Pending',
            1 => 'Confirmed',
            2 => 'Rescheduled',
            3 => 'Canceled'
        ],
        'STATUS_INTERNAL' => [
            'pending' => 0,
            'confirmed' => 1,
            'rescheduled' => 2,
            'canceled' => 3
        ],
        'PRICING_SCHEMA' => [
            0 => 'Standard'
        ]
    ],

    'CONVEYANCE_FEES' => 0, // Conveyance charges will be 0 for aggreagtor

    'HEALTH_OFFER_REQUEST_STATUS' => [
        0 => 'Requested',
        1 => 'Approved',
        2 => 'Rejected',
    ],
    'SPECIALITY_QUERY_STATUS' => [
        0 => 'Requested',
        1 => 'Processing',
        2 => 'Processed',
        3 => 'Rejected',
    ],

    'DOCTOR_SCHEDULE_STATUS' => [
        0 => 'Processing',
        1 => 'Processed',
        2 => 'Failed'
    ],
    'REPORTS_TYPE' => [
        1 => 'Appointment',
        2 => 'Finance',
        3 => 'Refund'
    ],
    'REFUND_STATUS' => [
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Completed',
        3 => 'Canceled'
    ],

    'CREATION_SOURCE' => [
        0 => 'HIS',
        1 => 'Agent Portal',
        2 => 'App',
        // Aggreagtor Source only onwords, format: Aggregator (Camel cased without space) Check
        3 => 'Aggregator (3rdParty)',
        // 4 => 'Aggregator (Practo)',
        // 5 => 'Aggregator (triptotreat)',
        // 6 => 'Aggregator (credihealth)',
        // 7 => 'Aggregator (lybrate)',
        // 8 => 'Aggregator (vidalhealth)',
        // 9 => 'Aggregator (Alexa)',
        // 10=> 'Aggregator (HDFCergo)',
        // 13=> 'Aggregator (Praktice)'
    ],

    'CREATION_SOURCE_INTERNAL' => [
        'his' => 0,
        'agent_portal' => 1,
        'app' => 2,
        // Aggreagtor Source only onwords, format: aggregator_<lowercased name without space>
         'aggregator_3rdparty' => 3,
        // 'aggregator_practo' => 4,
        // 'aggregator_triptotreat' => 5,
        // 'aggregator_credihealth' => 6,
        // 'aggregator_lybrate' => 7,
        // 'aggregator_vidalhealth' => 8,
        // 'aggregator_alexa' => 9,
        // 'aggregator_hdfcergo' => 10,
        // 'aggregator_praktice' => 13,
    ],
];
