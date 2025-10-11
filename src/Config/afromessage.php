<?php

return [
    'token' => env('AFROMESSAGE_TOKEN', ''),
    'base_url' => env('AFROMESSAGE_BASE_URL', 'https://api.afromessage.com/api/'),
    'sender_id' => env('AFROMESSAGE_SENDER_ID', ''),
    'sender_name' => env('AFROMESSAGE_SENDER_NAME', ''),
    'timeout' => env('AFROMESSAGE_TIMEOUT', 120),
];