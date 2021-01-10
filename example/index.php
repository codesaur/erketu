<?php

/* DEV: v9.2021.01.08
 * 
 * This is an example script!
 */

use codesaur\Http\Request;
use codesaur\Http\Response;
use codesaur\HTML\Template;

$autoload = require_once '../vendor/autoload.php';
$autoload->addPsr4('erketu\\Example\\', \dirname(__FILE__));

class ExampleResponse extends Response
{
    public function error(string $message, int $status = 404, \Throwable $t = null)
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

(new erketu\Example\Application(new Request(), new ExampleResponse()))->handle();
