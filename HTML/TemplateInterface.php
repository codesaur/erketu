<?php namespace codesaur\HTML;

interface TemplateInterface
{
    public function source($html);
    
    public function file(string $filepath);
    
    public function setArray(array $values);
    public function set(string $key, $value);
    public function enhance(string $key, $value);
    public function sets(array $keys, string $value = '');
    
    public function has(string $key);
    public function &get(string $key);
    public function &getVars() : array;
    public function getSource();
    public function getFileName();
    
    public function reset();
    
    public function render();
    public function output() : string;
    
    public function stringify($content) : string;
    
    public function raw(string $template) : string;
}
