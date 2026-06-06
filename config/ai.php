<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    'base_url' => 'https://generativelanguage.googleapis.com/v1beta',

    'prompts' => [
        'outline' => 'Create a detailed table of contents with 5-7 chapters for a book about: {topic}. Return as JSON with chapters array containing title and description.',
        'content' => 'Write a comprehensive section for a book chapter titled "{title}". Make it professional and 500-800 words long.',
    ],
];
