<?php namespace codesaur\Generic;

interface OutputBufferInterface
{
    public function start($output_callback = null, $chunk_size = 0, $erase = PHP_OUTPUT_HANDLER_STDFLAGS);
    public function end();
    public function endFlush();
    public function getLength() : int;
    public function getContents() : string;
    public function compress($buffer);
}
