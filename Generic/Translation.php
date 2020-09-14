<?php namespace codesaur\Generic;

class Translation extends Base implements TranslationInterface
{
    public $text;
    public $load;

    function __construct()
    {
        $this->reset();
    }
    
    public function append(string $name, array $values) : bool
    {
        if (\in_array($name, $this->load)) {
            return false;
        }

        $this->load[] = $name;
        $this->text += $values; // I choose + operator over array_merge because of performance speed

        return true;
    }

    public function reset()
    {
        $this->text = array();
        $this->load = array();
    }
}
