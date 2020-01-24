<?php

return [
    'blood_group' => [
        [
            'id' => 1,
            'name' => 'O+',
            'mapped' => 'O+ve'
        ],
        [
            'id' => 2,
            'name' => 'O-',
            'mapped' => 'O-ve'
        ],
        [
            'id' => 3,
            'name' => 'A+',
            'mapped' => 'A+ve'
        ],
        [
            'id' => 4,
            'name' => 'A-',
            'mapped' => 'A-ve'
        ],
        [
            'id' => 5,
            'name' => 'B+',
            'mapped' => 'B+ve'
        ],
        [
            'id' => 6,
            'name' => 'B-',
            'mapped' => 'B-ve'
        ],
        [
            'id' => 7,
            'name' => 'AB+',
            'mapped' => 'AB+ve'
        ],
        [
            'id' => 8,
            'name' => 'AB-',
            'mapped' => 'AB-ve'
        ],
    ],
    'gender' => [
        '1' => 'Male',
        '2' => 'Female',
        '3' => 'Other',
    ],
    'aggregator_types' => [
        '3rdParty',
        // 'practo',
        // 'triptotreat',
        // 'credihealth',
        // 'lybrate',
        // 'vidalhealth',
        // 'alexa',
        // 'hdfcergo',
        // 'praktice',
    ],
    'api_secret_keys' => [
        '3rdParty' => env('AGG_SECRET_KEY_1'),
        // 'practo' => env('AGG_SECRET_KEY_2'),
        // 'triptotreat' => env('AGG_SECRET_KEY_3'),
        // 'credihealth' => env('AGG_SECRET_KEY_4'),
        // 'lybrate' => env('AGG_SECRET_KEY_5'),
        // 'vidalhealth' => env('AGG_SECRET_KEY_6'),
        // 'alexa' => env('AGG_SECRET_KEY_7'),
        // 'hdfcergo' => env('AGG_SECRET_KEY_8'),
        // 'praktice' => env('AGG_SECRET_KEY_9'),
    ],
    'code_prefix' => [
        'master_patient' => 'MHID',
        'doctor' => 'DOCTID',
        'speciality' => 'SPECID',
        'hospital' => 'HOSPID',
    ],
    'appointment_add_days' =>30,
    'timezone' => [
        'user' => env('DEFAULT_TIMEZONE', 'Asia/Kolkata')
    ],
    'new_patient_pagination' => 4,
    'booking_status' => [
        'pending' => 0,
        'confirmed' => 1,
        'rescheduled' => 2,
        'canceled' => 3
    ],
];
