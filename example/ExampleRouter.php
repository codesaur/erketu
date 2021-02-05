<?php

namespace erketu\Example;

use Psr\Http\Message\ServerRequestInterface;

use codesaur\Http\Router;

class ExampleRouter extends Router
{
    function __construct()
    {
        $exampleController = ExampleController::class;
        
        $this->get('/hello/:firstname', [$exampleController, 'hello'], 'hello');
        $this->map(['POST', 'PUT'], '/post-or-put', [$exampleController, 'post_put']);
        
        $this->any('/echo/:singleword', function (ServerRequestInterface $req)
        {
            echo $req->getAttribute('singleword');
        });
    }
}
