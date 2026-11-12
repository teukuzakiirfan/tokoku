<?php

return [
    'name' => env('STORE_NAME', 'TOKOKU POS'),
    'tagline' => env('STORE_TAGLINE', 'Minimarket & Retail Modern'),
    'address' => env('STORE_ADDRESS', 'Jl. Sudirman No. 88, Jakarta Selatan'),
    'phone' => env('STORE_PHONE', '(021) 789-1234'),
    'low_stock' => 10,         // stok <= angka ini ditandai "Sisa N"
    'paper_width' => '58mm',   // lebar kertas struk thermal: 58mm atau 80mm
    'categories' => ['Makanan', 'Minuman', 'Snack', 'Sembako', 'Kebutuhan Rumah', 'Perawatan Pribadi', 'Obat & Kesehatan'],
    'payment_methods' => ['Tunai', 'QRIS', 'Debit/Transfer'],
];
