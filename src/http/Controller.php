<?php namespace codesaur\Http;

use codesaur\Base\Base;

class Controller extends Base
{
    private $_request;
    private $_response;
    
    function __construct(Request $request, Response $response)
    {
        $this->_request = $request;
        $this->_response = $response;
    }
    
    public function request() : Request
    {
        return $this->_request;
    }
    
    public function response() : Response
    {
        return $this->_response;
    }

    public function getNick() : string
    {
        return \str_replace($this->getMeClean(__CLASS__), '', $this->getMeClean());
    }
}
