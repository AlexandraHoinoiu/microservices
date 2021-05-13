<?php

return [
    'use_proxy' => env('AWS_USE_PROXY', 0),

    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID', ''),
        'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
    ],
    'bucket' => env('AWS_S3_BUCKET', ''),
    'region' => env('AWS_REGION', 'eu-west-2'),
    'version' => 'latest'
];
