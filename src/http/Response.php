<?php namespace codesaur\Http;

use codesaur\Base\Base;
use codesaur\HTML\FileTemplate;
use codesaur\Base\Language;
use codesaur\Base\Translation;
use codesaur\Base\OutputBuffer;

class Response extends Base
{
    private $_header;
    private $_output;
    
    private $_language;
    private $_translation;
    
    public function __construct()
    {
        $this->_header = new Header();
        $this->_output = new OutputBuffer();
        
        if (\getenv('OUTPUT_COMPRESS', true) == 'true') {
            $this->getBuffer()->startCompress();
        } else {
            $this->getBuffer()->start();
        }
        
        $this->_language = new Language();
        $this->_translation = new Translation();
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
    
    public function &language() : Language
    {
        return $this->_language;
    }
    
    public function &translation() : Translation
    {
        return $this->_translation;
    }

    public function header($content)
    {
        return $this->getHeader()->send($content);
    }
    
    public function redirect(string $url, int $status = 302)
    {
        if ($this->header($status)) {
            $this->header("Location: $url");
        } else {
            return null;
        }
    }
    
    public function json($data, bool $isapp = true)
    {
        $this->header('Content-Type: ' . ($isapp ? 'application' : 'text') . '/json');
        
        echo \json_encode($data);
    }
    
    public function render($content, int $status = 200)
    {
        $this->header($status);
        
        if ($content instanceof FileTemplate) {
            $content->render();
        } else {
            echo $content;
        }
    }
    
    public function error(string $message, int $status = 404)
    {
        $this->header($status);
        
        \error_log("Error[$status]: $message");
        
        $host = (( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $host .= $_SERVER['HTTP_HOST'] ?? 'localhost';

        die(    '<!doctype html><html lang="en"><head><meta charset="utf-8"/>' .
                "<title>Error $status</title></head><body><h1>Error $status</h1>" .
                "<p>$message</p><hr><a href=\"$host\">$host</a></body></html>");
    }
}
