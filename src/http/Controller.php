<?php namespace codesaur\Http;

class Controller
{
    private $_request;
    private $_response;
    
    function __construct(Request $request, Response $response)
    {
        $this->_request = $request;
        $this->_response = $response;
    }
    
    public function request(): Request
    {
        return $this->_request;
    }
    
    public function response(): Response
    {
        return $this->_response;
    }
}
