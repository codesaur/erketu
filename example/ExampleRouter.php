<?php namespace erketu\Example;

use codesaur\Http\Router;

class ExampleRouter extends Router
{
    function __construct()
    {
        $this->map('/', 'erketu\\Example\\ExampleController');
        $this->map('/post-or-put', 'post_put@erketu\\Example\\ExampleController', ['methods' => ['POST', 'PUT']]);
        $this->map('/hello/:firstname', 'hello@erketu\\Example\\ExampleController', ['filters' => ['firstname' => '(\w+)']]);
        
        $this->any('/echo/:singleword', function($req) { echo $req->params->singleword; });
    }
}
