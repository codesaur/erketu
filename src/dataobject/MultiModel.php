<?php

namespace codesaur\DataObject;

use PDO;
use PDOStatement;

class MultiModel extends InitableModel
{
    public $content;
 
    public $key  = 't_id';
    public $flag = 'code';

    function __construct(CDO $conn)
    {
        parent::__construct($conn);

        $this->content = new Model($conn);
    }

    public function setTables(string $primary, $content = null): bool
    {
        $this->content->setTable($content ?? "{$primary}_content");
        
        return parent::setTable($primary);
    }

    public function structures(MultiDescribe $describes)
    {
        parent::structure($describes->primary);
        
        $this->content->structure(
                (new Describe())->create(
                        array(
                           (new Column('inc', 'bigint', 20))->auto()->primary()->unique()->notNull(),
                           (new Column($this->key, 'bigint', 20))->notNull(),
                            new Column($this->flag, 'varchar', 6)
                        )
                )
        );
        
        foreach ($describes->content->getColumns() as $column) {
            $this->content->describe->addColumn($column);
        }
    }

    public function second(): Model
    {
        return $this->content;
    }
    
    public function inserts(
            array $primary,
            array $content
    ) {
        $id = parent::insert($primary);
        if ($id !== false) {
            foreach ($content as $code => $value) {
                $value[$this->key] = $id;
                $value[$this->flag] = $code;
                if ($this->content->insert($value) === false) {
                    // Should we roll back insertion from primary table & in content codes?
                }
            }
        }
        
        return $id;
    }
    
    public function updates(
            array  $primary,
            array  $content,
            array  $where = ['primary' => [], 'content' => []],
            string $condition = ''
    ) {
        $id = parent::update($primary, $where['primary'], $condition);
        if ($id !== false) {
            if (empty($where['content'])) {
                $where['content'] = [$this->key, $this->flag];
            }
            
            foreach ($content as $key => $value) {
                $value[$this->key] = $primary[$this->primary];
                $value[$this->flag] = $key;
                if ($this->content($value, $where['content']) === false) {
                    // Should we roll back update from primary table & in content codes?
                }
            }
        }
        
        return $id;
    }

    public function replaces(
            array  $primary,
            array  $content,
            string $keyColumnName = '_keyword_'
    ) {
        if (isset($primary[$keyColumnName])) {
            $existing = $this->getBy($keyColumnName, $primary[$keyColumnName]);
            if ($existing) {
                $primary[$this->primary] = $existing[$this->primary];

                return $this->updates($primary, $content);
            }
        }
        
        return $this->inserts($primary, $content);
    }

    public function content(
            array $content,
            array $where,
                  $replace = true
    ) {
        $columns = array();
        foreach ($where as $name) {
            if ($this->content->describe->hasColumn($name)) {
                $columns[$name] = $this->content->describe->getColumn($name);
                $columns[$name]->setValue($content[$name]);
            }
        }
        
        $stmt = $this->content->select('*', $columns);
        if ($stmt->rowCount()) {
            if ($replace) {
                return $this->content->update($content, $where);
            }            
        } else {
            return $this->content->insert($content);
        }
        
        return false;
    }
    
    public function selectjoin($selection, array $condition = []): PDOStatement
    {
        $condition['JOIN'] =
                'p INNER JOIN `' . $this->content->getTable() . "` c ON p.$this->primary=c.$this->key";
        
        return parent::select($selection, [], $condition);
    }
    
    public function statement(array $primary = [], array $content = [], array $condition = []): PDOStatement
    {
        if ($primary === array() && $content === array()) {
            $selection = '*';
        } else {
            $selection = '';
            
            if (empty($primary)) {
                $primary = $this->describe->getColumnKeys();
            }
            
            if (empty($content)) {
                $content = $this->content->describe->getColumnKeys();
            }
            
            foreach ($primary as $name) {
                if ($selection != '') {
                    $selection .= ', ';
                }
                $selection .= 'p.' . $name;
            }
            
            foreach ($content as $name) {
                if ($selection != '') {
                    $selection .= ', ';
                }
                $selection .= 'c.' . $name;
            }
        }
        
        return $this->selectjoin($selection, $condition);
    }

    public function statementBy($id, string $flag = null): PDOStatement
    {
        if (!$this->describe->getColumn($this->primary)->isIntType()) {
            $id = $this->dataobject()->quote($id);
        }
        
        $clause = "p.$this->primary=$id";
        if (isset($flag)) {
            $clause .= " AND c.$this->flag=" . $this->dataobject()->quote($flag);
        }
        
        return $this->selectjoin('*', ['WHERE' => $clause]);
    }

    public function getFirst($condition = null)
    {
        $record = parent::getFirst($condition);
        
        if (isset($record[$this->primary])) {
            return $this->getByID($record[$this->primary]);
        }
        
        return null;
    }

    public function getBy(string $name, string $value)
    {
        if ($this->describe->hasColumn($name)) {
            $column = $this->describe->getColumn($name);

            $stmt = $this->dataobject()->prepare(
                    'SELECT * FROM ' . $this->getTable()
                    . ' WHERE ' . $column->getName() . '=' . $column->getBindName());
            $stmt->bindParam(
                    $column->getBindName(), $value, $column->getDataType(),
                    $column->needLength() ? $column->getLength(): null);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $this->getByID($result[$this->primary]);
            }
        }
        
        return null;
    }

    public function getByID($value, $flag = null): array
    {
        $record = array();
        $pdostmt = $this->statementBy($value, $flag);
        while ($data = $pdostmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($record[$this->primary])) {
                foreach ($this->describe->getColumns() as $column) {
                    if (isset($data[$column->getName()])) {
                        $record[$column->getName()] = $data[$column->getName()];
                    } else {
                        $record[$column->getName()] = $column->getDefault();
                    }
                }
            }
            
            foreach ($this->content->describe->getColumns() as $ccolumn) {
                if ($ccolumn->getName() != $this->key
                        && $ccolumn->getName() != $this->flag
                        && $ccolumn->getName() != $this->content->primary) {
                    if (isset($data[$ccolumn->getName()])) {
                        $record[$ccolumn->getName()][$data[$this->flag]] = $data[$ccolumn->getName()];
                    } else {
                        $record[$ccolumn->getName()][$data[$this->flag]] = $ccolumn->getDefault();
                    }
                }
            }
        }
        
        return $record;
    }
    
    public function getRows(array $condition = []): array
    {
        if (empty($condition)) {
            $condition = ['ORDER BY' => $this->primary];
        }
        
        return $this->getStatementRows($this->selectjoin('*', $condition));
    }
    
    public function getStatementRows(PDOStatement $pdostmt): array
    {
        $rows = array();
        while ($data = $pdostmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($rows[$data[$this->primary]][$this->primary])) {
                foreach ($this->describe->getColumns() as $column) {
                    if (isset($data[$column->getName()])) {
                        $rows[$data[$this->primary]][$column->getName()] = $data[$column->getName()];
                    } else {
                        $rows[$data[$this->primary]][$column->getName()] = $column->getDefault();
                    }
                }
            }
            
            foreach ($this->content->describe->getColumns() as $ccolumn) {
                if ($ccolumn->getName() != $this->key
                        && $ccolumn->getName() != $this->flag
                        && $ccolumn->getName() != $this->content->primary) {
                    if (isset($data[$ccolumn->getName()])) {
                        $rows[$data[$this->primary]][$ccolumn->getName()][$data[$this->flag]] = $data[$ccolumn->getName()];
                    } else {
                        $rows[$data[$this->primary]][$ccolumn->getName()][$data[$this->flag]] = $ccolumn->getDefault();
                    }
                }
            }
        }
        
        return $rows;
    }

    public function deletes(array $columns, array $flags)
    {
        if (getenv('DB_KEEP_DATA', true) == 'true'
                && $this->describe->hasColumn('is_active')) {
            $result = parent::delete($columns);
            if ($result) {
                if (!$this->describe->getColumn($this->primary)->isNumeric()) {
                    $old_id = $this->dataobject()->quote(
                            substr($result[$this->primary], strlen(uniqid()) + 3));
                    foreach ($flags as $flag) {
                        $record = [$this->key => $result[$this->primary]];
                        $this->content->update(
                                $record, array(),
                                "$this->key=$old_id AND $this->flag=" . $this->dataobject()->quote($flag));
                    }
                }
                
                return true;
            }
        } else {
            $pdostmt = $this->select('*', $columns);
            $row = $pdostmt->fetch(PDO::FETCH_ASSOC);
            $t_id = $this->content->describe->getColumn($this->key);
            $code = $this->content->describe->getColumn($this->flag);
            foreach ($flags as $flag) {
                $code->setValue($flag);
                $t_id->setValue($row[$this->primary]);
                $this->content->delete(array($t_id, $code));
            }
            
            return parent::delete($columns);
        }
        
        return false;
    }
    
    public function copy(string $flagA, string $flagB)
    {
        $content_cols = [];
        foreach ($this->content->describe->getColumns() as $column) {
            if ($column->getName() != $this->content->primary) {
                $content_cols[] = $column->getName();
            }
        }
        
        $pdostmt = $this->statement(
                array($this->primary), $content_cols,
                array('WHERE' => "c.$this->flag=" . $this->dataobject()->quote($flagA)));
        $pdostmt->execute();
        
        if ($pdostmt->rowCount() > 0) {
            unset($content_cols[$this->key]);
            unset($content_cols[$this->flag]);
            
            $content = array();
            while ($row = $pdostmt->fetch(PDO::FETCH_ASSOC)) {
                foreach ($content_cols as $column) {
                    if (isset($row[$column])) {
                        $content[$column] = $row[$column];
                    } else {
                        $content[$column] = '';
                    }
                }
                $content[$this->flag] = $flagB;
                $content[$this->key] = $row[$this->key];
                $this->content($content, array($this->key, $this->flag));
            }
        }
    }
}
