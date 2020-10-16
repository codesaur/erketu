<?php namespace codesaur\Base;

interface ApplicationInterface
{
    public function initComponents();
    
    public function getNamespace();
    public function getWebUrl(bool $relative) : string;
    
    public function launch();
    public function error(string $message, int $status = 404);
    public function execute($class, string $action, array $args);    
}
