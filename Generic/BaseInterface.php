<?php namespace codesaur\Generic;

interface BaseInterface
{
    public function callFunc(callable $callback, $parameter = null);
    public function callFuncArray(callable $callback, array $param_arr);
    
    public function getCaller() : string;
    public function getClass($object = null) : string;
    public function getMe() : string;
    public function getMeClean($name = null) : string;
    public function getNick() : string;
    public function getParent($object = null) : string;
    
    public function hasMethod(string $method) : bool;
    public function hasProperty(string $property) : bool;
    
    public function loadClass(string $name);
    
    public function isCallable($method) : bool;
    public function isEmpty($var) : bool;
}
