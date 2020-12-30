<?php namespace codesaur\Base;

interface ApplicationInterface
{
    public function launch();
    public function execute($class, string $action, array $args);    
    public function error(string $message, int $status_code = 404);
    
    public function getNamespace();
    public function getConfiguraton();
    public function getWebUrl(bool $relative) : string;
    public function getPublicUrl(bool $relative = true) : string;
    public function getResourceUrl(bool $relative = true) : string;
}
