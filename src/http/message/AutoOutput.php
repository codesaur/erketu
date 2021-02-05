<?php declare(strict_types=1);

namespace codesaur\Http\Message;

use RuntimeException;

use Psr\Http\Message\StreamInterface;

use codesaur\Base\OutputBuffer;

class AutoOutput implements StreamInterface
{    
    protected $output;
    
    public function __construct()
    {
        $this->output = new OutputBuffer();
        
        if (getenv('OUTPUT_COMPRESS', true) === 'true') {
            $this->output->startCompress();
        } else {
            $this->output->start();
        }
    }
    
    function __destruct()
    {
        // If outbut buffering is still active when the script ends, PHP outputs it automatically.
        // In effect, every script ends with ob_end_flush(). Thus we don't really need to call endFlush!
        // $this->output->endFlush();
    }
    
    public function getBuffer(): OutputBuffer
    {
        return $this->output;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->output->end();
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $this->output->end();
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        $this->output->getLength();
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return RuntimeException(__CLASS__ . ' is not seekable!');
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return RuntimeException(__CLASS__ . ' is not rewindable!');
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        $content = (string)$string;
        
        echo $content;
        
        return strlen($content);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        throw new RuntimeException(__CLASS__ . ' is not readable!');
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        return (string)$this->output->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        return null;
    }
}
