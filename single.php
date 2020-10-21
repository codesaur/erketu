<?php

/**
 * codesaur
 *
 * An elegant object-oriented application development framework for PHP 7.1 or newer
 *
 * @version   7
 * @package   Erketu
 * @author    Narankhuu N <codesaur@gmail.com>, +976 99000287
 * @copyright Copyright (c) 2012 - 2020. Munkhiin Ololt LLC. +976 99000287, contact@ololt.mn, https://ololt.mn
 *
 * @creature  Erketu (meaning "Erketü Tengri -> creator-god" / монголоор "Эрхэт тэнгэр") is a genus of somphospondylan sauropod dinosaur
 *            that lived in Asia during the Late Cretaceous roughly between 102 million and 86 million years ago. Its fossils were found in Mongolia
 *            between 2002 and 2003 during a field expedition and first described in 2006; later on in 2010 due to some cervicals that were left behind in the expedition.
 *            Erketu represent one of the first sauropods described from the Bayan Shireh Formation. The elongated cervical vertebrae of Erketu seemsto indicate that it was the sauropod with the longest neck relative to its body size.
 */

final class codesaur
{
    private static $_application;

    public static function start(codesaur\Base\Application $app)
    {
        self::$_application = $app;

        self::app()->launch();
    }
    
    public static function app() : codesaur\Base\Application
    {
        return self::$_application;
    }

    public static function request() : codesaur\Http\Request
    {
        return self::app()->request;
    }
    
    public static function router() : codesaur\Http\Router
    {
        return self::app()->router;
    }
    
    public static function header() : codesaur\Http\Header
    {
        return self::app()->header;
    }
    
    public static function response() : codesaur\Http\Response
    {
        return self::app()->response;
    }
    
    public static function buffer() : codesaur\Base\OutputBuffer
    {
        return self::response()->ob;
    }

    public static function route() : ?codesaur\Http\Route
    {
        return self::app()->route ?? null;
    }

    public static function controller()
    {
        return self::app()->controller ?? null;
    }
    
    public static function link(string $route, array $params = []) : string
    {
        $url = self::router()->generate($route, $params);

        if (empty($url)) {
            return 'javascript:;';
        }

        return self::request()->getPathComplete() . $url[0];
    }

    public static function redirect(string $route, array $params = [])
    {
        if ( ! self::router()->check($route)) {
            self::app()->error("Can't redirect to invalid route [$route]!");
        }

        $url = self::request()->getPathComplete();
        $url .= self::router()->generate($route, $params)[0];
        
        self::header()->redirect($url);
    }

    public static function user() : codesaur\Base\User
    {
        return self::app()->user;
    }

    public static function helper() : codesaur\Base\Helper
    {
        return self::app()->helper;
    }

    public static function session() : codesaur\Globals\Session
    {
        return self::app()->session;
    }

    public static function language() : codesaur\Base\Language
    {
        return self::app()->language;
    }
    
    public static function translation() : codesaur\Base\Translation
    {
        return self::app()->translation;
    }

    public static function text($key) : string
    {
        return self::translation()->value($key);
    }

    public static function error($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_USER_ERROR:
                error_log("Error: $errstr \n Fatal error on line $errline in file $errfile \n");
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

    public static function about() : string
    {
        return 'codesaur - framework';
    }

    public static function author() : string
    {
        return 'Narankhuu N, codesaur@gmail.com, +976 99000287, Munkhiin Ololt LLC';
    }
}

set_error_handler('\codesaur::error');
