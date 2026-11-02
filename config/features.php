<?php

return [
    // Kill switch deploy: matikan SELURUH fitur reseller/afiliasi (routes,
    // menu, halaman) tanpa perlu rollback kode. Harus SAMA dengan nilai di
    // larashop-be (config/features.php) - kalau beda, salah satu sisi bisa
    // nampilin link/menu ke rute yang 404 di sisi lain.
    'reseller' => env('RESELLER_FEATURE_ENABLED', false),
];
