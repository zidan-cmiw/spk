<?php

/**
 * Redirect root access to Laravel's public directory
 */
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if (strpos($uri, '/public') === false) {
    header('Location: public/');
    exit;
}
require __DIR__ . '/public/index.php';
