<?php namespace codesaur\Common;

interface ApplicationInterface
{
    public function getNamespace();
    
    public function launch();
    public function execute($class, string $action, array $args);
    public function error(string $message, int $status = 404);
    
    public function webUrl(bool $relative) : string;
    public function publicUrl(bool $relative = true) : string;
    public function resourceUrl(bool $relative = true) : string;
}
