<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'smartping' => [
        'url' => env('SMARTPING_API_URL', 'https://backend.api-wa.co/campaign/smartping/api/v2'),
        'api_key' => env('SMARTPING_API_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjZhYTNjOTIwOTk0NDg2MTM0ZjNiZDE0ZiIsIm5hbWUiOiJWaWduYW4gVW5pdmVyc2l0eSBUZWNoIiwiYXBwTmFtZSI6IkFpU2Vuc3kiLCJjbGllbnRJZCI6IjZhYTNjOTIwOTk0NDg2MTM0ZjNiZDE0OCIsImFjdGl2ZVBsYW4iOiJQUk9fTU9OVEhMWSIsImlhdCI6MTc4OTExOTk3M30.nn4R60DwW9HbNBrpfjwJstXeephZrQssorJnQSsRaxg'),
        'user_name' => env('SMARTPING_USER_NAME', 'Vignan University Tech'),
        'source' => env('SMARTPING_SOURCE', 'new-landing-page form'),
        'otp_campaign' => env('SMARTPING_OTP_CAMPAIGN', 'crmadm'),
        'otp_mode' => env('SMARTPING_OTP_MODE', 'ONE_VAR'),
        'admission_campaign' => env('SMARTPING_ADMISSION_CAMPAIGN', 'crmfirstinfo'),
    ],

    'jiocx_sms' => [
        'url' => env('JIOCX_SMS_URL', 'https://smsapi.jiocx.com/apggw/vignans/sms/v1/send'),
        'username' => env('JIOCX_SMS_USERNAME', 'jiocx-vignans'),
        'password' => env('JIOCX_SMS_PASSWORD', ''),
        'sender_id' => env('JIOCX_SMS_SENDER_ID', 'VIGNAN'),
        'dlt_entity_id' => env('JIOCX_DLT_ENTITY_ID', '1101385640000011255'),
        'template_otp' => env('JIOCX_TEMPLATE_ID_OTP', '1777179265526294215'),
        'template_create_account' => env('JIOCX_DLT_TEMPLATE_ID_Create_Account'),
        'template_admission' => env('JIOCX_DLT_TEMPLATE_ID_ADMISSION', '1777179354065677511'),
    ],

    'payu' => [
        'key' => env('PAYU_KEY', 'TrOeqi'),
        'salt' => env('PAYU_SALT', 'oUwlPbUgo653mTYzLfv2flsuCRdYqfkv'),
        'env_prod' => (int) env('PAYU_ENV_PROD', 0),
    ],

];
