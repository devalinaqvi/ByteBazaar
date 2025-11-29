<?php
// config/config.php: Return array for DB + app settings
return [
    'app' => [
        'name' => 'Byte Bazaar',
        'debug' => true,  // Toggle errors
        'base_url' => '/byte-bazaar'  // For links (adjust for subfolder)
    ],
    'db' => [
        'host' => 'localhost',
        'dbname' => 'byte_bazaar',  // Or 'computer_store' from outline
        'user' => 'devali',
        'pass' => 'devali',  // Empty for XAMPP/LAMP
        'charset' => 'utf8mb4'
    ],
    'session' => [
        'secure' => false,  // Set true on HTTPS
        'httponly' => true
    ]
    // Add mail, uploads later
];