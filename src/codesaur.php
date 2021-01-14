<?php version_compare(PHP_VERSION, '7.2', '>=') || die('codesaur need PHP 7.2 or newer.');

/**
 * codesaur
 *
 * An elegant object-oriented application development framework for PHP 7.2 or newer
 *
 * @version   9
 * @package   Erketu
 * @author    Narankhuu N <codesaur@gmail.com>, +976 99000287
 * @copyright Copyright (c) 2012 - 2021. Munkhiin Ololt LLC. +976 99000287, contact@ololt.mn, https://ololt.mn
 *
 * @creature  Erketu (meaning "Erketü Tengri -> creator-god" / монголоор "Эрхэт тэнгэр") is a genus of somphospondylan sauropod dinosaur
 *            that lived in Asia during the Late Cretaceous roughly between 102 million and 86 million years ago. Its fossils were found in Mongolia
 *            between 2002 and 2003 during a field expedition and first described in 2006; later on in 2010 due to some cervicals that were left behind in the expedition.
 *            Erketu represent one of the first sauropods described from the Bayan Shireh Formation. The elongated cervical vertebrae of Erketu seemsto indicate that it was the sauropod with the longest neck relative to its body size.
 */

if ( ! function_exists('codesaur_error_log')) {
    function codesaur_error_log($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_USER_ERROR:
                error_log("Errorw: $errstr \n Fatal error on line $errline in file $errfile \n");
                break;
            case E_USER_WARNING:
                error_log("Warning: $errstr \n in $errfile on line $errline \n");
                break;
            case E_USER_NOTICE:
                error_log("Notice: $errstr \n in $errfile on line $errline \n");
                break;
            default:
                if ($errno != 2048) {
                    error_log("#$errno: $errstr \n in $errfile on line $errline \n");
                }
                break;
        }

        return true;
    }
}

if ( ! function_exists('codesaur_environment')) {
    error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);

    ini_set('log_errors', 'On');
    
    $vendor_dir = dirname(__FILE__) . '/../../..';
    
    if (file_exists("$vendor_dir/../composer.json")) {
        ini_set('error_log', "$vendor_dir/../logs/code.log");
    } else {
        ini_set('error_log', dirname(__FILE__) . '/../logs/code.log');
    }
    
    set_error_handler('\codesaur_error_log');
    
    if (file_exists("$vendor_dir/../.env")) {
        Dotenv\Dotenv::create("$vendor_dir/..")->load();
    }
    
    define('DEVELOPMENT', getenv('APP_ENV', true) != 'production');
    
    ini_set('display_errors', DEVELOPMENT ? 'On' : 'Off');
    
    $timezone = getenv('TIME_ZONE', true);
    if ($timezone) {
        date_default_timezone_set($timezone);
    }
    
    define('_ACCOUNT_ID_', 'CODESAUR_ACCOUNT_ID');
    
    define('CODESAUR_FRAMEWORK', 'codesaur - framework');
    define('CODESAUR_AUTHOR', 'Narankhuu N, codesaur@gmail.com, +976 99000287, Munkhiin Ololt LLC');
} else {
    codesaur_environment();
}

if ( ! function_exists('codesaur_vardump')) {    
    function codesaur_vardump($var, bool $full = true)
    {
        if ( ! DEVELOPMENT) {
            return;
        }
        
        $debug = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        \var_dump(['file' => $debug[0]['file'] ?? '', 'line' => $debug[0]['line'] ?? '']);

        if ($full) {
            \var_dump($var);
        } elseif (\is_array($var)) {
            \print_r($var);
        } else {
            echo $var;
        }
    }
}
