<?php namespace codesaur\Http;

interface ResponseInterface
{
    public function start(callable $output_callback = null, int $chunk_size = 0, int $erase = PHP_OUTPUT_HANDLER_STDFLAGS);
    public function send();
    public function end();
    public function json($data, bool $isapp = false, bool $header = true);
}
