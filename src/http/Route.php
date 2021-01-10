<?php namespace codesaur\Http;

use codesaur\Base\Base;

class Route extends Base
{
    private $_pattern;
    
    private $_callback;
    
    private $_action;
    private $_controller;
    
    private $_params  = array();
    private $_filters = array();
    private $_methods = array('GET');
    
    public function getPattern() : string
    {
        return $this->_pattern;
    }
    
    public function setPattern(string $path)
    {
        $this->_pattern = \rtrim($path, '/');
    }
    
    public function getController() : string
    {
        return $this->_controller;
    }
    
    public function setController(string $controller)
    {
        $this->_controller = $controller;
    }
    
    public function getAction() : string
    {
        return $this->_action;
    }
    
    public function setAction(string $action)
    {
        $this->_action = $action;
    }
    
    public function getMethods() : array
    {
        return $this->_methods;
    }
    
    public function setMethods(array $methods)
    {
        $this->_methods = $methods;
    }
    
    public function setFilters(array $filters)
    {
        $this->_filters = $filters;
    }
    
    public function getFilters() : array
    {
        return $this->_filters;
    }
    
    public function getRegex()
    {
        return \preg_replace_callback('/:(\w+)/', array(&$this, 'substituteFilter'), $this->getPattern());
    }
    
    final function substituteFilter($matches) : string
    {
        if (isset($matches[1]) &&
                isset($this->_filters[$matches[1]])) {
            return $this->_filters[$matches[1]];
        }
        
        return '([\w-%]+)';
    }
    
    public function getParameters() : array
    {
        return $this->_params;
    }
    
    public function setParameters(array $parameters)
    {
        $this->_params = $parameters;
    }
    
    public function setControllerAction(string $target)
    {
        $pos = \strpos($target, '@');
        if ($pos === false) {
            $controller = $target;
        } else {
            if ($pos != 0) {
                $action = \substr($target, 0, $pos);
            }
            
            $controller = \substr($target, $pos + 1);
        }
        
        $this->setAction($action ?? 'index');
        $this->setController($controller ?? 'DefaultController');
    }
    
    public function setCallback(callable $callback)
    {
        $this->_callback = $callback;
    }
    
    public function getCallback()
    {
        return $this->_callback;
    }
    
    public function isCallable() : bool
    {
        return isset($this->_callback);
    }
}
