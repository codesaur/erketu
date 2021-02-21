<?php

namespace erketu\Example;

use Psr\Http\Message\ServerRequestInterface;

use codesaur\Http\Router\Router;

class ExampleRouter extends Router
{
    function __construct()
    {        
        $this->get('/hello/{firstname}', [ExampleController::class, 'hello'])->name('hi');
        
        $this->map(['POST', 'PUT'], '/post-or-put', [ExampleController::class, 'post_put']);
        
        $this->any('/echo/{singleword}', function (ServerRequestInterface $req)
        {
            echo $req->getAttribute('singleword');
        })->name('echo');
    }
}
