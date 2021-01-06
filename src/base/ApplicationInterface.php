<?php namespace codesaur\Base;

interface ApplicationInterface
{
    public function launch();
    public function execute($class, string $action, array $args);    
    public function error(string $message, int $status_code = 404);
    
    public function getNamespace();
    public function getConfiguraton();
    public function getBaseUrl(bool $relative) : string;
}
