<?php namespace codesaur\HTML;

use codesaur\Base\Base;
use codesaur\Base\OutputBuffer;

class Template extends Base
{
    protected $_html;
    protected $_file;
    protected $_vars;

    function __construct(string $template = null, array $vars = null)
    {
        $this->reset();

        if (isset($template)) {
            $this->file($template);
        }

        if (isset($vars)) {
            $this->setArray($vars);
        }
    }

    final public function __toString()
    {
        return $this->output();
    }

    public function source($html)
    {
        $this->_html = (string) $html;
    }

    public function file(string $filepath)
    {
        if ( ! $this->isEmpty($filepath)) {
            $this->_file = $filepath;

            $this->source('');
        }
    }

    public function setArray(array $values)
    {
        foreach ($values as $var => $value) {
            $this->set($var, $value);
        }
    }

    public function set(string $key, $value)
    {
        $this->_vars[$key] = $this->stringify($value);
    }

    public function enhance(string $key, $value)
    {
        if ($this->has($key)) {
            $this->_vars[$key] .= $this->stringify($value);
        } else {
            $this->set($key, $value);
        }
    }

    public function sets(array $keys, string $value = '')
    {
        foreach ($keys as $key) {
            $this->set($key, $value);
        }
    }
    
    final public function has(string $key)
    {
        return isset($this->getVars()[$key]);
    }

    final public function &get(string $key)
    {
        if ($this->has($key)) {
            return $this->_vars[$key];
        }
        
        if (DEBUG) {
            \error_log('UNDEFINED KEY {' . $this->getMeClean() .  "}: $key");
        }        
        
        $nulldata = null;
        return $nulldata;
    }

    public function &getVars() : array
    {
        return $this->_vars;
    }

    public function getSource()
    {
        return $this->_html;
    }

    public function getFileName()
    {
        return $this->_file;
    }

    public function reset()
    {
        $this->_html = '';
        $this->_file = null;
        $this->_vars = array();
    }

    protected function compile(string $html) : string
    {
        foreach ($this->getVars() as $key => $value) {
            $tagToReplace = "{@$key}";
            $html = \str_replace($tagToReplace, isset($value) ? $this->stringify($value) : '', $html);
        }
        
        return $html;
    }

    public function render()
    {
        echo $this->output();
    }

    public function output() : string
    {
        if ($this->isEmpty($this->getSource())) {
            if ( ! isset($this->_file)) {
                return 'Error settings of Template.';
            }

            if ( ! \file_exists($this->getFileName())) {
                $error = "Error loading template file ({$this->getFileName()}).";
                
                \error_log($error);
                
                return $error;
            }
            
            $buffer = new OutputBuffer();
            $buffer->start();
            
            include($this->getFileName());
            
            $this->source($buffer->getContents());
            
            $buffer->end();
        }

        return $this->compile($this->getSource());
    }
    
    public function stringify($content) : string
    {
        if (\is_array($content)) {
            $text = '';
            foreach ($content as $str) {
                $text .= $this->stringify($str);
            }
            return $text;
        } else {
            return (string) $content;
        }
    }
    
    public function raw(string $template) : string
    {
        if ( ! \file_exists($template)) {
            return $template . 'Error loading template, incorrect file name!';
        }

        $buffer = new OutputBuffer();
        $buffer->start();

        include($template);

        $content = $buffer->getContents();

        $buffer->end();

        return $content;
    }
}
