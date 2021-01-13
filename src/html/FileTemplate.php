<?php namespace codesaur\HTML;

use codesaur\Base\OutputBuffer;

class FileTemplate extends MemoryTemplate
{
    protected $_file = null;

    function __construct(?string $template = null, ?array $vars = null)
    {
        parent::__construct(null, $vars);
        
        if (isset($template)) {
            $this->file($template);
        }
    }

    public function file(string $filepath)
    {
        if ( ! $this->isEmpty($filepath)) {
            $this->_file = $filepath;
        }
    }
    
    public function getFileName() : ?string
    {
        return $this->_file;
    }

    public function getFileSource() : ?string
    {
        if (empty($this->getFileName())) {
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

        $fileSource = $buffer->getContents();

        $buffer->end();
        
        return $fileSource;
    }

    public function output() : string
    {
        $this->source($this->getFileSource());

        return $this->compile($this->getSource());
    }
}
