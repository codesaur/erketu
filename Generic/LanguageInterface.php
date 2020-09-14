<?php namespace codesaur\Generic;

interface LanguageInterface
{
    public function create(array $languages, string $alias);
    
    public function created() : bool;
    public function select(string $select);    
    public function get(string $key);
    public function check(string $key) : bool;
    public function confirm(string $key, string $onfail = 'en') : string;
    public function count() : int;
    public function complete();
    public function codes() : array;
    public function names() : array;
    public function short() : string;
    public function full(string $key = null) : string;
    public function current();
    public function code() : string;
    public function name() : string;
    public function getSession();
    public function getAlias() : string;        
}
