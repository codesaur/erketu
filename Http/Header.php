<?php namespace codesaur\Http;

use codesaur\Base\Base;

class Header extends Base
{
    const HTTP_OK        = 200;
    const HTTP_FOUND     = 302;
    const HTTP_NOT_FOUND = 404;
    
    private $_status;

    function __construct(int $status = Header::HTTP_OK)
    {
        $this->_status = $status;
    }
    
    public function location(string $url)
    {
        \header("Location: $url");
    }
    
    public function redirect(string $url, int $status = Header::HTTP_FOUND)
    {
        if ($status < 300 || $status > 399) {
            $status = Header::HTTP_FOUND;
        }
        
        if ($this->respond($status)) {
            $this->location($url);
            exit;
        }
        
        return null;
    }
    
    public function respond(int $status = null)
    {
        if ($this->sent()) {
            return false;
        }
        
        if (isset($status)) {
            $this->_status = $status;
        }
        
        \http_response_code($this->_status);
        
        return \http_response_code();
    }

    public function respond404()
    {
        return $this->respond(Header::HTTP_NOT_FOUND);
    }

    public function respondOK()
    {
        return $this->respond(Header::HTTP_OK);
    }

    public function response(string $msg)
    {
        \header($msg);
    }

    public function sent(string &$file = null, int &$line = null) : bool
    {
        return \headers_sent($file, $line);
    }
    
    public function status() : int
    {
        return $this->_status;
    }
    
    public function content(string $type)
    {
        \header("Content-Type: $type");
    }
    
    public function goBack()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->location($_SERVER['HTTP_REFERER']);
        }
    }
    
    public function goBackJS(int $number = 1)
    {
        echo "<script>history.go(-$number);</script>";
    }
    
    public function getEntityBody()
    {
        return \file_get_contents('php://input');
    }

    public function getEntityBodyJson(bool $assoc = false, int $depth = 512, int $options = 0)
    {
        return \json_decode($this->getEntityBody(), $assoc, $depth, $options);
    }
}
