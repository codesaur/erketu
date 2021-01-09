<?php namespace codesaur\Base;

use codesaur\Http\Router;
use codesaur\Http\Request;
use codesaur\Http\Response;
use codesaur\Http\Controller;

interface ApplicationInterface
{
    public function handle(Request &$request, Response &$response); 
    public function error(string $message, int $status_code = 404, \Throwable $t = null);
    
    public function route(string $path, string $target, array $args = array());
    
    public function getRouter() : Router;
    public function getController() : ?Controller;
    public function getBaseUrl(bool $relative = true) : string;
}
