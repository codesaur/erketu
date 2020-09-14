<?php namespace codesaur\DataObject;

interface DescribeInterface
{
    public function create(array $columns);
            
    public function reset();

    public function addColumn(Column $column);    
    public function hasColumn(string $name) : bool;
    public function &getColumn(string $name) : Column;
    public function setColumnValue(string $key, $value);
    
    public function &getColumns() : array;
    public function getColumnKeys() : array;
    public function getColumnNames() : array;
    public function getTwigColumns(array $record = array()) : array;
    
    public function getPostValues(array $flags = []) : array;
    
    public function getBindName(string $column) : string;
    public function getDataType(string $column) : int;
}
