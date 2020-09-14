<?php namespace codesaur\Http;

use codesaur\Generic\Base;
use codesaur\Generic\OutputBuffer;

class Response extends Base implements ResponseInterface
{
    public $ob;
    
    public function __construct()
    {
        $this->ob = new OutputBuffer();
    }
    
    public function start(callable $output_callback = null, int $chunk_size = 0, int $erase = PHP_OUTPUT_HANDLER_STDFLAGS)
    {
        $this->ob->start($output_callback, $chunk_size, $erase);
    }

    public function send()
    {
        $this->ob->endFlush();
    }
    
    public function end()
    {
        $this->ob->end();
    }

    public function json($data, bool $isapp = true, bool $header = true)
    {
        if ($header) {
            \header('Content-Type: ' . ($isapp ? 'application' : 'text') . '/json');
        }
        
        echo \json_encode($data);
    }
}
