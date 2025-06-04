<?php
// PHPUnit bootstrap file

// Set error reporting to strict
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Composer autoload (adjust path if needed)
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    fwrite(STDERR, "Composer autoload not found: $autoload\n");
    exit(1);
}

// Additional test environment setup can go here
