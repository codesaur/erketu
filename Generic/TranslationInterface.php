<?php namespace codesaur\Generic;

interface TranslationInterface
{
    public function append(string $name, array $values) : bool;
    public function reset();
}
