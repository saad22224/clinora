<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Toggle optional integrations without deleting their source code.
    | Defaults are disabled for the online SaaS marketing launch.
    |
    */

    'features' => [
        'ai_enabled' => (bool) env('CLINORA_AI_ENABLED', false),
        'whatsapp_enabled' => (bool) env('CLINORA_WHATSAPP_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Clinic day timezone (queue numbering / "today")
    |--------------------------------------------------------------------------
    */
    'clinic_timezone' => env('CLINORA_CLINIC_TIMEZONE', 'Africa/Cairo'),

    'staff_quick_messages' => [
        'أدخل المريض التالي.',
        'توقف مؤقتًا عن إدخال المرضى.',
        'استأنف إدخال المرضى.',
        'المريض التالي جاهز.',
        'يرجى التواصل معي.',
        'أحتاج إلى السكرتارية.',
        'تأخر المريض الحالي.',
    ],

    /*
    |--------------------------------------------------------------------------
    | QA Data Factory
    |--------------------------------------------------------------------------
    |
    | Safety controls for the dedicated clinora_qa dataset generator.
    | Writes remain disabled until CLINORA_QA_ALLOW is explicitly enabled
    | on an environment whose active database name is clinora_qa.
    |
    */

    'qa' => [
        'allowed_database' => env('CLINORA_QA_DATABASE', 'clinora_qa'),
        'allow_writes' => (bool) env('CLINORA_QA_ALLOW', false),
        /*
         * Medical-file disk used when QA generation writes synthetic PDF bytes.
         * Defaults to the app "local" (private) disk so downloads work when the
         * application is pointed at clinora_qa. Generation is refused unless the
         * active database name is clinora_qa, so the live clinora DB is never targeted.
         */
        'files_disk' => env('CLINORA_QA_FILES_DISK', 'local'),
        'files_prefix' => env('CLINORA_QA_FILES_PREFIX', 'medical-records/qa'),
    ],

    'gemini_api_keys' => array_values(array_filter([
        env('GEMINI_API'),
        env('GEMINI_API2'),
        env('GEMINI_API3'),
        env('GEMINI_API4'),
    ])),

    'superadmin' => [
        'name' => env('SUPERADMIN_NAME', 'مدير النظام'),
        'email' => env('SUPERADMIN_EMAIL'),
        'password' => env('SUPERADMIN_PASSWORD'),
    ],

];
