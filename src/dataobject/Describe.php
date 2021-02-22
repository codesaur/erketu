<?php

namespace codesaur\DataObject;

class Describe
{
    private $_columns;

    public function create(array $columns)
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
        
        return $this;
    }

    public function reset()
    {
        $this->_columns = array();
    }
    
    public function addColumn(Column $column)
    {
        $this->_columns[$column->getName()] = $column;
    }
    
    public function hasColumn(string $name): bool
    {
        return isset($this->getColumns()[$name]);
    }

    public function &getColumn(string $name): Column
    {
        return $this->getColumns()[$name];
    }
    
    public function setColumnValue(string $key, $value)
    {
        $this->_columns[$key]->setValue($value);
    }

    public function &getColumns(): array
    {
        return $this->_columns;
    }

    public function getColumnKeys(): array
    {
        return array_keys($this->getColumns());
    }
        
    public function getColumnNames(): array
    {
        $names = array();
        foreach ($this->getColumns() as $key => $column) {
            $names[$key] = $column->getName();
        }
        
        return $names;
    }
    
    public function getTwigColumns(array $record = []): array
    {
        $twigcolumns = array();
        foreach ($this->getColumns() as $column) {
            $twigcolumns[$column->getName()] = array(
                'name' => $column->getPostName(),
                'length' => $column->getLength(),
                'type' => $column->getInputType(),
                'value' => $record[$column->getName()] ?? $column->getDefault()
            );
        }
        
        return $twigcolumns;
    }

    public function getPostValues(array $flags = []): array
    {
        $values = array();
        foreach ($this->getColumns() as $column) {
            if ($column->hasPost()) {
                $mixed = $column->getPost();
                if (is_array($mixed)) {
                    foreach ($mixed as $key => $value) {
                        if (in_array($key, $flags)) {
                            $values[$key][$column->getName()] = $value;
                        } else {
                            $values[$column->getName()][$key] = $value;
                        }
                    }
                } else {
                    $values[$column->getName()] = $mixed;
                }
            }
        }
        
        return $values;
    }

    public function getBindName(string $column): string
    {
        return $this->getColumns()[$column]->getBindName();
    }

    public function getDataType(string $column): int
    {
        return $this->getColumns()[$column]->getDataType();
    }   
}
