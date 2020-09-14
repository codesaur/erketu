<?php namespace codesaur\MultiModel;

interface InitableInterface
{
    public function initial() : bool;
    public function recover(string $name);
}
