<?php namespace codesaur\Common;

abstract class Base
{
    public function callFunc(callable $callback, $parameter = null)
    {
        return \call_user_func($callback, $parameter);
    }
        
    public function callFuncArray(callable $callback, array $param_arr)
    {
        return \call_user_func_array($callback, $param_arr);
    }

    public function getCaller() : string
    {
        return \get_called_class();
    }
    
    public function getClass($object = null) : string
    {
        return \get_class($object);
    }
    
    public function getMe() : string
    {
        return $this->getClass($this);
    }
    
    public function getMeClean($name = null) : string
    {
        if ( ! isset($name)) {
            $name = $this->getMe();
        }
        
        if (($last = \strrpos($name, '\\')) !== false) {
            $name = \substr($name, $last + 1);
        }
        
        return $name;
    }
    
    public function getNick() : string
    {
        return __CLASS__;
    }
            
    public function getParent($object = null)  : string
    {
        return \get_parent_class($object ?? $this);
    }
    
    public function hasMethod(string $method) : bool
    {
        return \method_exists($this, $method);
    }

    public function hasProperty(string $property) : bool
    {
        return \property_exists($this, $property);
    }

    public function loadClass(string $name, $arg = null)
    {
        $class = \str_replace(' ', '', $name);
        if (\class_exists($class)) {
            return isset($arg) ? new $class($arg) : new $class();
        }
        
        return null;
    }
    
    public function isCallable($method) : bool
    {
        return \is_callable(array($this->getMe(), $method));
    }

    public function isEmpty($var) : bool
    {
        if ( ! isset($var)) {
            return true;
        }
        
        if (empty($var)) {
            return true;
        }
        
        return \ctype_space($var);
    }
}
