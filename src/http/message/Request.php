<?php declare(strict_types=1);

namespace codesaur\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRequestTarget()
    {}

    
    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {}

    
    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {}

    
    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {}

    
    /**
     * {@inheritdoc}
     */
    public function getUri()
    {}

    
    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {}
}
