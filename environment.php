<?php version_compare(PHP_VERSION, '7.1', '>=') || die('codesaur need PHP 7.1 or newer.');

if ( ! function_exists('codesaur_environment')) {
    error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);

    $vendor_dir = dirname(__FILE__) . "/../..";

    try {
        Dotenv\Dotenv::create("$vendor_dir/..")->load();
    } catch (Exception $ex) {
        error_log($ex->getMessage());
    } finally {
        define('DEBUG', getenv('APP_ENV') != 'production');
    }

    ini_set('log_errors', 'On');
    ini_set('display_errors', DEBUG ? 'On' : 'Off');
    ini_set('error_log', "$vendor_dir/../logs/code.log");

    $timezone = getenv('TIME_ZONE');
    if ($timezone) {
        date_default_timezone_set($timezone);
    }

    define('_ACCOUNT_ID_', 'CODESAUR_ACCOUNT_ID');
} else {
    codesaur_environment();
}
