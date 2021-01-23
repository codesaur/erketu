<?php declare(strict_types=1);

namespace codesaur\Http\Message;

use Psr\Http\Message\ResponseInterface;

class Response extends Message implements ResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {}

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {}

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {}
}
    