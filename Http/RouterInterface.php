<?php namespace codesaur\Http;

interface RouterInterface
{
    public function map(string $routeUrl, string $target, array $args);
    public function match(string $cleanedUrl, string $method);
    public function check(string $routeName) : bool;
    public function generate(string $routeName, array $params) : array;
}
