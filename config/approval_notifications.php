<?php

return [
    'enabled' => env('APPROVAL_NOTIFICATIONS_ENABLED', true),

    'in_app_enabled' => env('APPROVAL_NOTIFICATIONS_IN_APP_ENABLED', true),

    'mail' => [
        'enabled' => env('APPROVAL_NOTIFICATIONS_MAIL_ENABLED', true),
        'queue' => env('APPROVAL_NOTIFICATIONS_MAIL_QUEUE', 'mail'),
    ],

    'modules' => [
        'attendance' => env('APPROVAL_NOTIFICATIONS_ATTENDANCE_ENABLED', true),
        'department' => env('APPROVAL_NOTIFICATIONS_DEPARTMENT_ENABLED', true),
        'salary' => env('APPROVAL_NOTIFICATIONS_SALARY_ENABLED', true),
        'user' => env('APPROVAL_NOTIFICATIONS_USER_ENABLED', true),
    ],
];
