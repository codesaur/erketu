<?php namespace codesaur\DataObject;

class InitableModel extends Model implements InitableInterface
{
    public function setTable(string $name): bool
    {
        if ( ! parent::setTable($name)) {
            return false;
        }

        $this->initial();
        
        return true;
    }
    
    public function initial(): bool
    {
        return false;
    }
    
    public function recover(string $name)
    {
        if ($this->getTable() != $name) {
            if ($this instanceof MultiModel) {
                $this->setTables($name);
            } else {
                $this->setTable($name);
            }
            
            $this->initial();
        }
    }
}
