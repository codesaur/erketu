<?php namespace codesaur\MultiModel;

interface MultiModelInterface
{
    public function setTables(string $primary, $content = null) : bool;
    
    public function structures(MultiDescribe $describes);
    
    public function second();
    
    public function inserts(array $primary, array $content);
    public function updates(
            array $primary, array $content,
            array $where = ['primary' => [], 'content' => []], string $condition = '');
    public function replaces(
            array $primary, array $content, string $keyColumnName = '_keyword_');
    public function deletes(array $columns, array $flags);
    
    public function content(array $content, array $where, $replace = true);
    
    public function selectjoin($selection, array $condition = []) : \PDOStatement;
    public function statement(
            array $primary = [], array $content = [], array $condition = []) : \PDOStatement;    
    public function statementBy($id, string $flag = null) : \PDOStatement;
    
    public function copy(string $flagA, string $flagB);
}
