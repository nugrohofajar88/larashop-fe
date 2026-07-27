<?php

return [
    // 'account' (default): flow existing - login + cart/checkout via BE API.
    // 'whatsapp': guest cart di session, checkout redirect ke WA, tidak ada order dibuat.
    'checkout_mode' => env('CHECKOUT_MODE', 'account'),
];
