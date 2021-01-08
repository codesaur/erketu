<?php namespace My\Test\App;

use codesaur\HTML\Template;
use codesaur\Http\Controller;

class ErrorController extends Controller
{
    public function error(string $message, int $status)
    {
        // credits to template
        // Author: Tibix
        // August 27, 2019
        // NEON - 404 PAGE NOT FOUND
        
        (new Template(\dirname(__FILE__) . '/neon.html', array('message' => $message)))->render();
    }
}
