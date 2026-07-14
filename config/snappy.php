<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    |
    | This option contains settings for PDF generation.
    |
    | Enabled:
    |    
    |    Whether to load PDF / Image generation.
    |
    | Binary:
    |    
    |    The file path of the wkhtmltopdf / wkhtmltoimage executable.
    |
    | Timeout:
    |    
    |    The amount of time to wait (in seconds) before PDF / Image generation is stopped.
    |    Setting this to false disables the timeout (unlimited processing time).
    |
    | Options:
    |
    |    The wkhtmltopdf command options. These are passed directly to wkhtmltopdf.
    |    See https://wkhtmltopdf.org/usage/wkhtmltopdf.txt for all options.
    |
    | Env:
    |
    |    The environment variables to set while running the wkhtmltopdf process.
    |
    */
    
    'pdf' => [
        'enabled' => true,
        'binary'  => env('WKHTML_PDF_BINARY', match (PHP_OS_FAMILY) {
            'Windows' => public_path('wkhtmltopdf.exe'),
            'Darwin'  => '/usr/local/bin/wkhtmltopdf',       // macOS (Homebrew Intel)
            'Linux'   => '/usr/local/bin/wkhtmltopdf',
            default   => base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
        }),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

    'image' => [
        'enabled' => true,
        'binary'  => env('WKHTML_IMG_BINARY', match (PHP_OS_FAMILY) {
            'Windows' => public_path('wkhtmltoimage.exe'),
            'Darwin'  => '/usr/local/bin/wkhtmltoimage',      // macOS (Homebrew Intel)
            'Linux'   => '/usr/local/bin/wkhtmltoimage',
            default   => base_path('vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64'),
        }),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];