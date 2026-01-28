<?php
// Configuration file for PDF delivery system

$config = [
    'passwords' => [
        'member' => 'member',
        'admin' => 'admin'
    ],
    'pdf_file' => 'nb.pdf',
    'cookie_name' => 'cookie_name',
    'cookie_lifetime' => 30 * 24 * 60 * 60 // 30 days in seconds
];
?>