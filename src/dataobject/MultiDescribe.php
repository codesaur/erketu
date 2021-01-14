<?php namespace codesaur\DataObject;

class MultiDescribe
{
    public $primary;
    public $content;
    
    public function create(array $primary, array $secondary)
    {
        $this->primary = (new Describe())->create($primary);        
        $this->content = (new Describe())->create($secondary);
        
        return $this;
    }
    
    public function getPrimary(): Describe
    {
        return $this->primary;
    }
    
    public function getContent(): Describe
    {
        return $this->content;
    }
}
