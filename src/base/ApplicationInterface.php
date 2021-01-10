<?php namespace codesaur\Base;

use codesaur\Http\Router;
use codesaur\Http\Request;
use codesaur\Http\Response;

interface ApplicationInterface
{
    public function handle(Request $request, Response $response);
    
    public function &router() : Router;
    
    public function any(string $path, callable $callback, ?string $name = null);
    public function get(string $path, callable $callback, ?string $name = null);
    public function post(string $path, callable $callback, ?string $name = null);
    public function put(string $path, callable $callback, ?string $name = null);
    public function patch(string $path, callable $callback, ?string $name = null);
    public function delete(string $path, callable $callback, ?string $name = null);
}
