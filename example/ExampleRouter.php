<?php namespace erketu\Example;

use codesaur\Http\Router;

class ExampleRouter extends Router
{
    function __construct()
    {
        $namespace = __NAMESPACE__;
        
        $this->map('/post-or-put', "post_put@$namespace\\ExampleController", ['methods' => ['POST', 'PUT']]);
        $this->map('/hello/:firstname', "hello@$namespace\\ExampleController", ['filters' => ['firstname' => '(\w+)']]);
        
        $this->any('/echo/:singleword', function($req) { echo $req->params->singleword; });
    }
}
