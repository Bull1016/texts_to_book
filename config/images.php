<?php

return [
    'default' => env('IMAGE_PROVIDER', 'gemini'),

    'providers' => [
        'unsplash' => [
            'api_key' => env('UNSPLASH_API_KEY'),
            'base_url' => 'https://api.unsplash.com',
        ],
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => 'imagen-4.0-generate-001',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        ],
    ],

    'quality' => env('IMAGE_QUALITY', 'regular'),
    'width' => env('IMAGE_WIDTH', 800),
    'height' => env('IMAGE_HEIGHT', 600),
    'timeout' => (int) env('IMAGE_TIMEOUT', 120),
];
