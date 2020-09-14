<?php namespace codesaur\Http;

interface RouteInterface
{
    public function getPattern() : string;
    public function setPattern(string $path);
    public function getController() : string;
    public function setController(string $controller);
    public function getAction() : string;
    public function setAction(string $action);
    public function getMethods() : array;
    public function setMethods(array $methods);
    public function setFilters(array $filters);
    public function getFilters() : array;
    public function getRegex();
    public function substituteFilter($matches) : string;
    public function getParameters() : array;
    public function setParameters(array $parameters);
    public function setControllerAction(string $target);
}
