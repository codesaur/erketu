<?php namespace codesaur\Globals;

class Cookie extends superGlobal
{
    public function has(string $var_name): bool
    {
        return parent::has_var(INPUT_COOKIE, $var_name);
    }

    public function value(string $var_name, int $filter = FILTER_DEFAULT, $options = null)
    {
        return parent::filter(INPUT_COOKIE, $var_name, $filter, $options);
    }

    public function direct(): array
    {
        return $_COOKIE;
    }

    public function raw($var_name)
    {
        return $_COOKIE[$var_name];
    }
}
