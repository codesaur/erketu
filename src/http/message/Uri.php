<?php declare(strict_types=1);

namespace codesaur\Http\Message;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {}

    /**
     * {@inheritdoc}
     */
    public function getAuthority()
    {}

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {}

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {}

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {}

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {}

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {}

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {}

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme)
    {}

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null)
    {}

    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {}

    /**
     * {@inheritdoc}
     */
    public function withPort($port)
    {}

    /**
     * {@inheritdoc}
     */
    public function withPath($path)
    {}

    /**
     * {@inheritdoc}
     */
    public function withQuery($query)
    {}

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment)
    {}

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {}
}
