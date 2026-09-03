<?php

$localConfig = __DIR__ . '/paystack_config.local.php';

if (file_exists($localConfig)) {
    require_once $localConfig;
} else {
    define('PAYSTACK_PUBLIC_KEY', '');
    define('PAYSTACK_SECRET_KEY', '');
}