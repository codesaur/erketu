<?php namespace codesaur\DataObject;

interface TableInterface
{
    function __construct(CDO $conn);
    
    public function dataobject() : MySQL;
    
    public function structure(Describe $structure);
    
    function create(string $name, array $columns) : int;
    
    public function getTable();
    public function getTableClean();
    public function getDescribe() : Describe;

    public function setTable(string $name) : bool;
    
    public function setVersion(string $name = null);
    public function getVersion();
    
    public function verify(array &$record, array $keys);
    
    public function insert(array $record);
    public function update(array $record, array $where = [], string $condition = '');
    
    public function select(string $selection = '*', array $columns = [], array $condition = []) : \PDOStatement;
    
    public function selectBy(array $params, array $condition = []) : \PDOStatement;
    
    public function where(array $columns, string $sql = '') : string;
    
    public function delete(array $columns);
    public function deactivate(array $columns);
    public function deleteBy(array $params);
    public function deleteByID($value);
    
    public function getFirst($condition = null);
    public function getRows(array $condition = []) : array;
    public function getStatementRows(\PDOStatement $pdostmt) : array;
    public function getBy(string $name, string $value);
    public function getRowBy(array $columns);
    public function getByID($value);
    
    public function checkBy(string $name, $value);
    public function checkByID($value);
}
