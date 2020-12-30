<?php version_compare(PHP_VERSION, '7.2', '>=') || die('codesaur need PHP 7.2 or newer.');

if ( ! function_exists('codesaur_environment')) {
    error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);

    $vendor_dir = dirname(__FILE__) . '/../../..';

    try {
        Dotenv\Dotenv::create("$vendor_dir/..")->load();
    } catch (Exception $ex) {
        error_log($ex->getMessage());
    } finally {
        define('DEBUG', getenv('APP_ENV', true) != 'production');
    }

    ini_set('log_errors', 'On');
    ini_set('display_errors', DEBUG ? 'On' : 'Off');
    ini_set('error_log', "$vendor_dir/../logs/code.log");

    $timezone = getenv('TIME_ZONE', true);
    if ($timezone) {
        date_default_timezone_set($timezone);
    }

    define('_ACCOUNT_ID_', 'CODESAUR_ACCOUNT_ID');
} else {
    codesaur_environment();
}
