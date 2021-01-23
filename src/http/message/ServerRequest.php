<?php declare(strict_types=1);

namespace codesaur\Http\Message;

use Psr\Http\Message\ServerRequestInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {}

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {}

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {}

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {}

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {}

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {}

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {}

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {}

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {}

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {}

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {}

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {}

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {}
}
