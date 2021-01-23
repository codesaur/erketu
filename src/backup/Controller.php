<?php

namespace codesaur\Backup;

class Controller
{
    private $_request;
    private $_response;
    
    function __construct(ServerRequest $request, Response $response)
    {
        $this->_request = $request;
        $this->_response = $response;
    }
    
    public function request(): ServerRequest
    {
        return $this->_request;
    }
    
    public function response(): Response
    {
        return $this->_response;
    }
}
