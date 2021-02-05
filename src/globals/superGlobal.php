<?php declare(strict_types=1);

namespace codesaur\Globals;

use InvalidArgumentException;

abstract class superGlobal
{
    public function has_var(int $type, string $var_name): bool
    {
        return filter_has_var($type, $var_name);
    }

    public function filter(int $type, string $var_name, int $filter = FILTER_DEFAULT, $options = null)
    {
        return filter_input($type, $var_name, $filter, $options);
    }
    
    public function filter_var($var, int $filter = FILTER_DEFAULT, $options = null)
    {
        if (empty($var)) {
            throw new InvalidArgumentException('Filter variable must be set!');
        }
        
        return filter_var($var, $filter, $options);
    }

    public function filter_array(int $type, $definition = null, bool $add_empty = true)
    {
        return filter_input_array($type, $definition, $add_empty);
    }
}
