<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Browsershot Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for Spatie\Browsershot package used for PDF generation
    |
    */

    'timeout' => env('BROWSERSHOT_TIMEOUT', 60),

    'pdf' => [
        'format' => env('BROWSERSHOT_PDF_FORMAT', 'A4'),
        'orientation' => env('BROWSERSHOT_PDF_ORIENTATION', 'landscape'),
        'margins' => [
            'top' => env('BROWSERSHOT_PDF_MARGIN_TOP', 18),
            'right' => env('BROWSERSHOT_PDF_MARGIN_RIGHT', 18),
            'bottom' => env('BROWSERSHOT_PDF_MARGIN_BOTTOM', 18),
            'left' => env('BROWSERSHOT_PDF_MARGIN_LEFT', 18),
        ],
    ],

    'chrome_args' => [
        '--disable-web-security',
        '--disable-dev-shm-usage',
        '--no-sandbox',
        '--disable-gpu',
        '--disable-software-rasterizer',
        '--run-all-compositor-stages-before-draw',
        '--disable-background-timer-throttling',
        '--disable-backgrounding-occluded-windows',
        '--disable-renderer-backgrounding',
        '--disable-features=VizDisplayCompositor',
    ],

    'viewport' => [
        'width' => env('BROWSERSHOT_VIEWPORT_WIDTH', 1280),
        'height' => env('BROWSERSHOT_VIEWPORT_HEIGHT', 1024),
    ],
];
