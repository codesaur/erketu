<?php namespace codesaur\Http;

use codesaur\Base\Base;

class Controller extends Base
{
    public function getNick() : string
    {
        return \str_replace($this->getMeClean(__CLASS__), '', $this->getMeClean());
    }
    
    final public function vardump($var, bool $full = true)
    {
        if ( ! DEBUG) {
            return;
        }
        
        $debug = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        \var_dump(['file' => $debug[0]['file'] ?? '', 'line' => $debug[0]['line'] ?? '']);

        if ($full) {
            \var_dump($var);
        } elseif (\is_array($var)) {
            \print_r($var);
        } else {
            echo $var;
        }
    }
}
