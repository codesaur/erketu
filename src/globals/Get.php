<?php namespace codesaur\Globals;

class Get extends superGlobal
{
    public function has(string $var_name): bool
    {
        return parent::has_var(INPUT_GET, $var_name);
    }

    public function value(string $var_name, int $filter = FILTER_DEFAULT, $options = null)
    {
        return parent::filter(INPUT_GET, $var_name, $filter, $options);
    }

    public function direct(): array
    {
        return $_GET;
    }

    public function raw($var_name)
    {
        return $_GET[$var_name];
    }
}
