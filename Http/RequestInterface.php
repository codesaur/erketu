<?php namespace codesaur\Http;

interface RequestInterface
{
    public function initFromGlobal();
    
    public function isSecure() : bool;

    public function getDomain() : string;
    public function getHttpHost() : string;
    public function getUrl() : string;
    public function getCleanUrl() : string;
    public function getParams() : array;
    public function getParamsAsStr() : string;
    public function getParam($key);
    public function hasParam($key) : bool;
    public function addParam($key, $value);
    public function getPath() : string;
    public function getMethod() : string;
    public function getScript() : string;
    public function getUrlSegments() : array;
    public function getPathComplete() : string;
    
    public function setApp(string $alias);

    public function cleanUrl(string $url, string $query_string) : string;
    public function forceCleanUrl(string $url);

    public function recursionParams(string $key, $value);
    
    public function shiftUrlSegments();
}
