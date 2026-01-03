<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | The stock quantity threshold below which a low stock notification
    | will be triggered.
    |
    */

    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 10),

    /*
    |--------------------------------------------------------------------------
    | Admin Email
    |--------------------------------------------------------------------------
    |
    | The email address to send low stock notifications and daily sales
    | reports to.
    |
    */

    'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),

    /*
    |--------------------------------------------------------------------------
    | Tax Rate
    |--------------------------------------------------------------------------
    |
    | The tax rate percentage to apply to cart subtotals.
    | Default is 8.5% (0.085 as decimal, but stored as percentage).
    |
    */

    'tax_rate' => env('TAX_RATE', 8.5),
];
