<?php namespace codesaur\Http;

use codesaur\Base\Base;
use codesaur\Base\OutputBuffer;

class Response extends Base
{
    public $_header;
    public $_output;
    
    public function __construct()
    {
        $this->_header = new Header();
        $this->_output = new OutputBuffer();
    }
    
    public function &getBuffer() : OutputBuffer
    {
        return $this->_output;
    }

    public function &getHeader() : Header
    {
        return $this->_header;
    }

    public function header($content)
    {
        $this->getHeader()->send($content);
    }
    
    public function redirect(string $url, int $status = 302)
    {
        if ($this->getHeader()->send($status)) {
            $this->getHeader()->send("Location: $url");
        } else {
            return null;
        }
    }
    
    public function json($data, bool $isapp = true, bool $header = true)
    {
        if ($header) {
            $this->header('Content-Type: ' . ($isapp ? 'application' : 'text') . '/json');
        }
        
        echo \json_encode($data);
    }
}
