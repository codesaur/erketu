<?php namespace codesaur\Base;

class OutputBuffer extends Base
{
    public function start($output_callback = null, $chunk_size = 0, $erase = PHP_OUTPUT_HANDLER_STDFLAGS)
    {
        \ob_start($output_callback, $chunk_size, $erase);
    }    
    
    public function end()
    {
        if (\ob_get_level()) {
            \ob_end_clean();
        }
    }
    
    public function endFlush()
    {
        if (\ob_get_level()) {
            \ob_end_flush();
        }
    }
    
    public function getLength() : int
    {
        return \ob_get_length();
    }

    public function getContents() : string
    {
        if ( ! \ob_get_level()) {
            return null;
        }        
        return \ob_get_contents();
    }
    
    public function compress($buffer)
    {
        $search = array(
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s');    // shorten multiple whitespace sequences
        
        return \preg_replace($search, array('>', '<', '\\1'), $buffer);
    }
}
