<?php

namespace codesaur\Http;

use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    private $_request;
    
    function __construct(ServerRequestInterface $request)
    {
        $this->_request = $request;
    }
    
    public function getRequest(): ServerRequestInterface
    {
        return $this->_request;
    }
}
