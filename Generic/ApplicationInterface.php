<?php namespace codesaur\Generic;

interface ApplicationInterface
{
    public function getNamespace();
    
    public function launch();
    public function execute($class, string $action, array $args);
    public function error(string $message, int $status = 404);
    
    public function webUrl(bool $global) : string;
    public function publicUrl(bool $global = false) : string;
    public function resourceUrl(bool $global = false) : string;
}
