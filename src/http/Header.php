<?php namespace codesaur\Http;

use codesaur\Base\Base;

class Header extends Base
{
    private $_status;

    function __construct(int $status = 200)
    {
        $this->_status = $status;
    }
 
    public function send($header, bool $replace = TRUE, int $http_response_code = NULL)
    {
        if (\is_int($header)) {
            if ($this->isSent()) {
                return false;
            }
            
            $this->_status = $header;
            
            \http_response_code($this->_status);

            return \http_response_code();
        } elseif (isset($header)) {
            \header($header, $replace, $http_response_code);
        }
    }

    function isSent(string &$file = null, int &$line = null) : bool
    {
        return \headers_sent($file, $line);
    }
    
    public function getStatus() : int
    {
        return $this->_status;
    }
}
