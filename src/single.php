<?php

/**
 * codesaur
 *
 * An elegant object-oriented application development framework for PHP 7.2 or newer
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

use codesaur\Base\User;
use codesaur\Http\Request;
use codesaur\Http\Response;
use codesaur\Base\Language;
use codesaur\Globals\Session;
use codesaur\Base\Translation;
use codesaur\Base\Application;

final class codesaur
{
    private static $_app;
    private static $_request;
    private static $_response;
    private static $_user;
    private static $_session;
    private static $_language;
    private static $_translation;
    
    public static function __initComponents()
    {
        self::$_request = new Request();
        self::$_request->initFromGlobal();
        self::$_response = new Response();
        
        self::$_user = new User();
        self::$_session = new Session();
        self::$_language = new Language();
        self::$_translation = new Translation();
    }
    
    public static function start(Application $app)
    {
        self::$_app = $app;

        self::app()->handle(self::$_request, self::$_response);
    }
    
    public static function app() : Application
    {
        return self::$_app;
    }

    public static function request() : Request
    {
        return self::$_request;
    }
    
    public static function &response() : Response
    {
        return self::$_response;
    }

    public static function user() : User
    {
        return self::$_user;
    }

    public static function session() : Session
    {
        return self::$_session;
    }

    public static function language() : Language
    {
        return self::$_language;
    }
    
    public static function translation() : Translation
    {
        return self::$_translation;
    }
    
    public static function link(string $route, array $params = []) : string
    {
        $url = self::app()->getRouter()->generate($route, $params);

        if (empty($url)) {
            return 'javascript:;';
        }

        return self::request()->getPathComplete() . $url[0];
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

codesaur::__initComponents();

set_error_handler('\codesaur::error');
