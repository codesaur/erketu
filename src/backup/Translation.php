<?php namespace codesaur\Backup;

class Translation
{
    public $text = array();
    
    public function create(string $name, array $values): bool
    {
        if (isset($this->text[$name])) {
            return false;
        }

        $this->text[$name] = $values;
        
        return true;
    }
    
    public function get(string $name): ?array
    {
        return $this->text[$name] ?? null;
    }
    
    public function value(string $key): string
    {
        foreach ($this->text as $translation) {
            if (isset($translation[$key])) {
                return $translation[$key];
            }
        }
        
        if (defined('CODESAUR_DEVELOPMENT')
                && CODESAUR_DEVELOPMENT
        ) {
            \error_log("UNTRANSLATED: $key");
        }
        
        return '{' . $key . '}';
    }
}
