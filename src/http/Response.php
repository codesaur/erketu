<?php namespace codesaur\Http;

use codesaur\Base\Base;
use codesaur\HTML\Template;
use codesaur\Base\OutputBuffer;

class Response extends Base
{
    public $_header;
    public $_output;
    
    public function __construct()
    {
        $this->_header = new Header();
        $this->_output = new OutputBuffer();
        
        if (\getenv('OUTPUT_COMPRESS', true) == 'true') {
            $this->getBuffer()->startCompress();
        } else {
            $this->getBuffer()->start();
        }
    }
    
    function __destruct()
    {
        $this->getBuffer()->endFlush();
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
    
    public function render($content)
    {
        if ($content instanceof Template) {
            $content->render();
        } else {
            echo $content;
        }
    }
    
    public function error(string $message, int $status = 404)
    {
        if ( ! \headers_sent()) {
            \http_response_code($status);
        }
        
        \error_log("Error[$status]: $message");
        
        $host = (( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $host .= $_SERVER['HTTP_HOST'] ?? 'localhost';

        echo    '<!doctype html><html lang="en"><head><meta charset="utf-8"/>' .
                "<title>Error $status</title></head><body><h1>Error $status</h1>" .
                "<p>$message</p><hr><a href=\"$host\">$host</a></body></html>";
    }
}
