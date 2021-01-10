<?php namespace codesaur\Http;

use codesaur\Base\Base;
use codesaur\Base\Application;

class Controller extends Base
{
    private $_application;
    
    function __construct(Application $app)
    {
        $this->_application = $app;
    }
    
    public function &app() : Application
    {
        return $this->_application;
    }
    
    public function &request() : Request
    {
        return $this->app()->request();
    }
    
    public function &response() : Response
    {
        return $this->app()->response();
    }

    public function getNick() : string
    {
        return \str_replace($this->getMeClean(__CLASS__), '', $this->getMeClean());
    }
}
