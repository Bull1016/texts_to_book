<?php

return [
    'default' => env('AI_DRIVER', 'openai'),

    'drivers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => 'https://api.openai.com/v1',
        ],
    ],

    'prompts' => [
        'outline' => 'Create a detailed table of contents with 5-7 chapters for a book about: {topic}. Return as JSON with chapters array containing title and description.',
        'content' => 'Write a comprehensive section for a book chapter titled "{title}". Make it professional and 500-800 words long.',
    ],
];
