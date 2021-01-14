<?php namespace codesaur\Globals;

class Env extends superGlobal
{
    public function has($var_name): bool
    {
        return isset($_ENV[$var_name]);
    }
    
    public function direct(): array
    {
        return $_ENV;
    }

    public function raw($var_name)
    {
        return $this->has($var_name) ? $_ENV[$var_name] : null;
    }
}
