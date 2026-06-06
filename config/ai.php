<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    'base_url' => 'https://generativelanguage.googleapis.com/v1beta',

    'prompts' => [
        'outline' => 'Create a detailed table of contents with 5-7 chapters for a book about: {topic}. Each chapter should have 2-3 sub-sections. The output must be in {language}. Return as JSON with a "chapters" array. Each chapter object must have "title" (without numbering), "description", and a "subsections" array of objects with "title" (without numbering) and "description".',
        'content' => 'You are writing a book about "{topic}". The full table of contents is: {outline}. Now, write a comprehensive and detailed content for the section titled "{title}" (which is part of the chapter "{chapter_title}"). Its description is: {description}. Make it professional, engaging, and about 500-800 words long. Use Markdown for formatting (bold, lists, etc.). The language must be {language}.',
    ],
];
