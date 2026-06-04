<?php

return [
    'engine' => env('PDF_ENGINE', 'dompdf'),

    'dompdf' => [
        'public_path' => public_path(),
        'path' => storage_path('pdf'),
        'font_dir' => base_path('resources/fonts'),
        'font_data_dir' => storage_path('fonts'),
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => public_path(),
        'allowed_protocols' => ['file://', 'http://', 'https://'],
        'enable_remote' => true,
        'enable_font_subsetting' => true,
    ],

    'paper' => env('PDF_PAPER', 'a4'),
    'orientation' => env('PDF_ORIENTATION', 'portrait'),
];
