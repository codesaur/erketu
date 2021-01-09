<?php namespace erketu\Example;

use codesaur\HTML\Template;
use codesaur\Http\Controller;

class ErrorController extends Controller
{
    public function error(string $message, int $status)
    {
        if ( ! \headers_sent()) {
            \http_response_code($status);
        }
        
        \error_log("Error[$status]: $message");
        
        // credits to template
        // Author: Tibix
        // August 27, 2019
        // NEON - 404 PAGE NOT FOUND
        (new Template(\dirname(__FILE__) . '/neon.html', array('message' => $message)))->render();
    }
}