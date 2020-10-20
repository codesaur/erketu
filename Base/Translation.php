<?php namespace codesaur\Base;

class Translation extends Base
{
    public $text;

    function __construct()
    {
        $this->reset();
    }
    
    public function create(string $name, array $values) : bool
    {
        if (isset($this->text[$name])) {
            return false;
        }

        $this->text[$name] = $values;
        
        return true;
    }
    
    public function get(string $name) : ?array
    {
        return $this->text[$name] ?? null;
    }
    
    public function value(string $key) : string
    {
        foreach ($this->text as $translation) {
            if (isset($translation[$key])) {
                return $translation[$key];
            }
        }
        
        if (DEBUG) {
            error_log("UNTRANSLATED: $key");
        }
        
        return '{' . $key . '}';
    }

    public function reset()
    {
        $this->text = array();
    }
}
