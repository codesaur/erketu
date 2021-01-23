<?php declare(strict_types=1);

namespace codesaur\Http\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

abstract class Message implements MessageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {}

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {}

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {}

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {}

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {}

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {}

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {}

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {}

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {}

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {}

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {}
}
