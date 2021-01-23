<?php namespace codesaur\Backup\DataObject;

interface InitableInterface
{
    public function initial(): bool;
    public function recover(string $name);
}
