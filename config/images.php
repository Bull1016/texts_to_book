<?php

return [
    'default' => env('IMAGE_PROVIDER', 'unsplash'),

    'providers' => [
        'unsplash' => [
            'api_key' => env('UNSPLASH_API_KEY'),
            'base_url' => 'https://api.unsplash.com',
        ],
    ],

    'quality' => env('IMAGE_QUALITY', 'regular'),
    'width' => env('IMAGE_WIDTH', 800),
    'height' => env('IMAGE_HEIGHT', 600),
];
