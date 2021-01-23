<?php

namespace erketu\Example;

use codesaur\Base\Router;

class ExampleRouter extends Router
{
    function __construct()
    {
        $exampleController = ExampleController::class;
        
        $this->map('/hello/:firstname', [$exampleController, 'hello']);
        $this->map(['POST', 'PUT'], '/post-or-put', [$exampleController, 'post_put']);
        
        $this->any('/echo/:singleword', function ($req) { echo $req->params->singleword; });
    }
}
