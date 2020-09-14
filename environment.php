<?php version_compare(PHP_VERSION, '7.1', '>=') || die('codesaur need PHP 7.1 or newer.');

if ( ! function_exists('codesaur_environment')) {
    error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);

    $vendor_dir = dirname(__FILE__) . "/../..";

    $domain_parts = \explode('.', $_SERVER['SERVER_NAME']);
    $domain_parts_count = count($domain_parts);
    if ($domain_parts_count > 2
            && $domain_parts[$domain_parts_count - 3] != 'www') {
        $env_file_name = '.env_' . $domain_parts[$domain_parts_count - 3];
    } else {
        $env_file_name = null;
    }

    try {
        Dotenv\Dotenv::create("$vendor_dir/..", $env_file_name)->load();
    } catch (Exception $ex) {
        error_log($ex->getMessage());
    } finally {
        define('DEBUG', getenv('APP_ENV') != 'production');
    }

    ini_set('log_errors', 'On');
    ini_set('display_errors', DEBUG ? 'On' : 'Off');
    ini_set('error_log', "$vendor_dir/../tmp/code.log");

    $timezone = getenv('TIME_ZONE');
    if ($timezone) {
        date_default_timezone_set($timezone);
    }

    define('_ACCOUNT_ID_', 'CODESAUR_ACCOUNT_ID');
} else {
    codesaur_environment();
}
