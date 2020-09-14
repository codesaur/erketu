<?php

/**
 * codesaur
 *
 * An elegant object-oriented application development framework for PHP 7.1 or newer
 *
 * @package   Swift seizer
 * @version   7
 * @author    Narankhuu N <codesaur@gmail.com>, +976 99000287
 * @copyright Copyright (c) 2012 - 2020. Munkhiin Ololt LLC. +976 99000287, contact@ololt.mn, https://ololt.mn
 *
 * @creatures Velociraptor (/vɪˈlɒsɪræptər/; meaning "swift seizer" in Latin) is a genus of dromaeosaurid theropod dinosaur
 *            that lived approximately 75 to 71 million years ago during the later part of the Cretaceous Period.
 *            Two species are currently recognized, although others have been assigned in the past.
 *            The type species is V. mongoliensis; fossils of this species have been discovered in Mongolia.
 *            A second species, V. osmolskae, was named in 2008 for skull material from Inner Mongolia.
 *
 *            The Indoraptor was a new hybrid dinosaur that served as the primary creature antagonist
 *            and the secondary antagonist of Jurassic World: Fallen Kingdom.
 */

final class codesaur
{
    private static $_application;

    public static function start(codesaur\Generic\Application $app)
    {
        self::$_application = $app;

        self::app()->launch();
    }
    
    public static function app() : codesaur\Generic\Application
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
    
    public static function buffer() : codesaur\Generic\OutputBuffer
    {
        return self::response()->ob;
    }

    public static function session() : codesaur\Globals\Session
    {
        return self::app()->session;
    }

    public static function route() : codesaur\Http\Route
    {
        return self::app()->route;
    }

    public static function controller()
    {
        return self::app()->controller;
    }

    public static function user() : codesaur\Generic\AuthUser
    {
        return self::app()->user;
    }

    public static function language() : codesaur\Generic\Language
    {
        return self::app()->language;
    }
    
    public static function translation() : codesaur\Generic\Translation
    {
        return self::app()->translation;
    }

    public static function flag()
    {
        return self::language()->current();
    }

    public static function text($key) : string
    {
        if (isset(self::translation()->text[$key])) {
            return self::translation()->text[$key];
        }

        if (DEBUG) {
            error_log("UNTRANSLATED: $key");
        }

        return '{' . $key . '}';
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
        return 'codesaur v7 - Swift seizer';
    }

    public static function author() : string
    {
        return 'Narankhuu N, codesaur@gmail.com, +976 99000287, Munkhiin Ololt LLC';
    }
}

set_error_handler('\codesaur::error');
