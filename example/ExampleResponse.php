<?php namespace erketu\Example;

use codesaur\Http\Response;
use codesaur\HTML\Template;

class ExampleResponse extends Response
{
    public function error(string $message, int $status = 404)
    {
        if ( ! \headers_sent()) {
            \http_response_code($status);
        }
        
        \error_log("Error[$status]: $message");
        
        (new Template(\dirname(__FILE__) . '/neon.html', array('message' => $message)))->render();
    }
}
