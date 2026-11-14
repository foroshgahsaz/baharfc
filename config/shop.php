<?php

return [

    'otp' => [
        'length' => (int) env('SHOP_OTP_LENGTH', 5),
        'expires_in_seconds' => (int) env('SHOP_OTP_EXPIRES', 120),
        'resend_cooldown_seconds' => (int) env('SHOP_OTP_RESEND_COOLDOWN', 60),
        'max_attempts' => (int) env('SHOP_OTP_MAX_ATTEMPTS', 5),
    ],

    'sms' => [
        'driver' => env('SHOP_SMS_DRIVER', 'log'),
    ],

];
