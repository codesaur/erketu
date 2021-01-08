<?php version_compare(PHP_VERSION, '7.2', '>=') || die('codesaur need PHP 7.2 or newer.');

if ( ! function_exists('codesaur_environment')) {
    error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);

    $vendor_dir = dirname(__FILE__) . '/../../..';
    
    ini_set('log_errors', 'On');
    ini_set('display_errors', DEBUG ? 'On' : 'Off');
    
    if (file_exists("$vendor_dir/../composer.json")) {
        ini_set('error_log', "$vendor_dir/../logs/code.log");
    } else {
        ini_set('error_log', dirname(__FILE__) . '/../logs/code.log');
    }
    
    if (file_exists("$vendor_dir/../.env")) {
        Dotenv\Dotenv::create("$vendor_dir/..")->load();
    }
    
    define('DEBUG', getenv('APP_ENV', true) != 'production');
    
    $timezone = getenv('TIME_ZONE', true);
    if ($timezone) {
        date_default_timezone_set($timezone);
    }
    
    define('_ACCOUNT_ID_', 'CODESAUR_ACCOUNT_ID');
} else {
    codesaur_environment();
}
