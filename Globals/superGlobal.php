<?php namespace codesaur\Globals;

use codesaur\Common\Base;

abstract class superGlobal extends Base
{
    public function has_var(int $type, string $var_name) : bool
    {
        return \filter_has_var($type, $var_name);
    }

    public function filter(int $type, string $var_name, int $filter = FILTER_DEFAULT, $options = null)
    {
        return \filter_input($type, $var_name, $filter, $options);
    }

    public function filter_array(int $type, $definition = null, bool $add_empty = true)
    {
        return \filter_input_array($type, $definition, $add_empty);
    }
}
