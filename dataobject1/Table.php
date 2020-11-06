<?php namespace codesaur\DataObject;

use codesaur\Base\Base;

class Table extends Base
{
    public $do;       // CDO - codesaur DataObject
    public $describe; // Table structure
    public $primary;  // Primary id column name
    
    protected $_sql_table;
    protected $_sql_table_versions;

    function __construct(CDO $conn)
    {
        $this->do = $conn;
    }
    
    public function structure(Describe $structure)
    {
        $this->describe = $structure;
        
        $index = \current($this->describe->getColumns());
        
        if ($index->isPrimary()) {
            $this->primary = $index->getName();
        }
    }
    
    function __destruct()
    {
        if ($this->dataobject()) {
            $this->do = null;
        }
    }
    
    public function __toString()
    {
        return $this->getTableClean();
    }
    
    public function dataobject() : MySQL
    {
        return $this->do;
    }
    
    function create(string $name, array $columns) : int
    {
        $sql = '';
        $special = '';
        $hasForeignKey = false;
        foreach ($columns as $field) {
            $sql .= ($sql != '') ? ', ' : '';
            $sql .= $field->getSQL($this->dataobject()->getCollation());
            if ($field->isPrimary()) {
                $special .= ($special != '') ? ', ' : '';
                $special .= 'PRIMARY KEY (`' . $field->getName() . '`)';
            }
            if ($field->isUnique()) {
                $special .= ($special != '') ? ', ' : '';
                $special .= 'UNIQUE (`' . $field->getName() . '`)';
            }
            if ($field->isAuto() && $field->isNumeric()) {
                $auto_increment = 1;
            }
            if ($field->hasForeignKey()) {
                $hasForeignKey = true;
                $foreign = $field->getForeignKey();
                $special .= ($special != '') ? ', ' : '';
                $special .= 'FOREIGN KEY (`' . (\is_array($foreign) ? \key($foreign) : $field->getName())
                    . '`)' . ' REFERENCES ' . (\is_array($foreign) ? \current($foreign) : $foreign);
            }
        }
        $query = "CREATE TABLE `$name` ($sql";
        if ($special != '') {
            $query .= ", $special";
        }
        $query .= ') ENGINE=' . $this->dataobject()->getEngine();
        $query .= ' DEFAULT CHARSET=' . $this->dataobject()->getCharset();
        $query .= ' COLLATE=' . $this->dataobject()->getCollation();
        
        if (isset($auto_increment)) {
            $query .= " AUTO_INCREMENT=$auto_increment;";
        }
        
        if ($hasForeignKey) {
            $this->dataobject()->exec('set foreign_key_checks=0');
        }
        
        return $this->dataobject()->exec($query);
    }

    public function getTable()
    {
        return $this->_sql_table;
    }

    public function getTableClean()
    {
        return $this->getTable();
    }
    
    public function getDescribe() : Describe
    {
        return $this->describe;
    }
    
    public function setTable(string $name) : bool
    {
        $this->_sql_table = \preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        
        if ($this->dataobject()->has($this->_sql_table)) {
            return false;
        }
        
        $this->create($this->_sql_table, $this->describe->getColumns());
        
        return true;
    }

    public function setVersion(string $name = null)
    {
        if ( ! $this->_sql_table) {
            return null;
        }
        
        $this->_sql_table_version = \preg_replace('/[^A-Za-z0-9_-]/', '', $name ?? "{$this->_sql_table}_version");
        
        if ( ! $this->dataobject()->has($this->_sql_table_version)) {
            if ($this->dataobject()->exec(
                    "CREATE TABLE `$this->_sql_table` LIKE `$this->_sql_table_version`") !== false) {
                $this->dataobject()->exec(
                        "ALTER TABLE `$this->_sql_table` ADD v_id bigint(20) NOT NULL, ADD v_number int(11) NOT NULL");
            } else {
                $this->_sql_table_version = null;
            }
        }
        
        return $this->getVersion();
    }
    
    public function getVersion()
    {
        return $this->_sql_table_version;
    }
    
    public function verify(array &$record, array $keys)
    {
        foreach ($keys as $key) {
            if ( ! $this->describe->hasColumn($key)) {
                unset($record[$key]);
            }
        }
    }

    public function insert(array $record)
    {
        $columns = \array_keys($record);
        $this->verify($record, $columns);
        
        $fields = $values = '';
        foreach ($columns as $name) {
            if ($values != '') {
                $fields .= ', ';
                $values .= ', ';
            }
            $fields .= $name;
            $values .= $this->describe->getBindName($name);
        }
        
        $stmt = $this->dataobject()->prepare(
                "INSERT INTO `{$this->getTable()}` ($fields) VALUES ($values)");
        
        foreach ($record as $key => $value) {
            $stmt->bindValue($this->describe->getBindName($key), $value, $this->describe->getDataType($key));
        }
        
        if ($stmt->execute()) {
            if (isset($record[$this->primary])) {
                $id = $record[$this->primary];
            } else {
                if ($this->describe->getColumn($this->primary)->isIntType()) {
                    $id = (int) $this->dataobject()->lastInsertId();
                } else {
                    $id = $this->dataobject()->lastInsertId();
                }
            }
        }
        
        return $id ?? false;
    }

    public function update(array $record, array $where = [], string $condition = '')
    {
        $columns = \array_keys($record);
        $this->verify($record, $columns);

        if ($this->isEmpty($where) && $this->isEmpty($condition)) {
            if ( ! isset($record[$this->primary])) {
                throw new \Exception('no primary index to update');
            }
            $where = [$this->primary];
        }
        
        $clause = '';
        foreach ($where as $name) {
            if (\in_array($name, $columns)) {
                if ($clause !=  '') {
                    $clause .= ' AND ';
                }
                $clause .= "$name=" . $this->describe->getBindName($name);
            }
        }
        
        if ( ! $this->isEmpty($condition)) {
            $clause .= ($clause == '') ? $condition : " AND $condition";
        }
        
        $set = '';
        $keys = \array_diff($columns, $where);
        foreach ($keys as $key) {
            if ($set != '') {
                $set .= ', ';
            }
            $set .=  "$key = " . $this->describe->getBindName($key);
        }
        
        if ($set == '') {
            if (isset($record[$this->primary])) {
                $stmt = $this->dataobject()->prepare(
                        'UPDATE `' . $this->getTable() . "` SET $this->primary=" .
                        $this->describe->getBindName($this->primary) . " WHERE $clause");
            } else {
                throw new \Exception('no primary record for update');
            }
        } else {
            $stmt = $this->dataobject()->prepare(
                    'UPDATE `' . $this->getTable() . "` SET $set WHERE $clause");
        }
        
        foreach ($record as $key => $value) {
            $stmt->bindValue(
                    $this->describe->getBindName($key), $value, $this->describe->getDataType($key));
        }
        
        if ($stmt->execute()) {
            if (isset($record[$this->primary])) {
                return $record[$this->primary];
            } else {
                return $stmt->rowCount();
            }
        }
        
        return false;
    }
    
    public function select(
            string $selection = '*', array $columns = [], array $condition = []) : \PDOStatement
    {
        $query = "SELECT $selection FROM `" . $this->getTable() . '`';        
        if (isset($condition['JOIN'])) {
            $query .= ' ' . $condition['JOIN'];
        }
        
        $query .= $this->where($columns, $condition['WHERE'] ?? '');
        
        if (isset($condition['ORDER BY'])) {
            $query .= ' ORDER BY ' . $condition['ORDER BY'];
        }
        
        if (isset($condition['LIMIT'])) {
            $query .= ' LIMIT ' . $condition['LIMIT'];
        }
        
        $pdostmt = $this->dataobject()->prepare($query);
        foreach ($columns as $column) {
            $pdostmt->bindValue($column->getBindName(), $column->getValue(), $column->getDataType());
        }
        $pdostmt->execute();

        return $pdostmt;
    }
    
    public function selectBy(array $params, array $condition = []) : \PDOStatement
    {
        $columns = [];
        foreach ($params as $key => $value) {
            if ($this->describe->hasColumn($key)) {
                $this->describe->setColumnValue($key, $value);
                $columns[] = $this->describe->getColumn($key);
            }
        }
        
        return $this->select('*', $columns, $condition);
    }

    public function where(array $columns, string $sql = '') : string
    {
        $clause = '';
        if ( ! empty($columns)) {
            foreach ($columns as $column) {
                if ($clause === '') {
                    $clause .= ' WHERE ';
                } else {
                    $clause .= ' AND ';
                }
                $clause .= $column->getName() . '=' . $column->getBindName();
            }
            if ($sql != '') {
                $clause .= " AND $sql";
            }
        } elseif ($sql != '') {
            $clause .= " WHERE $sql";
        }
        
        return $clause;
    }

    public function delete(array $columns)
    {
        if (\getenv('DB_KEEP_DATA', true) == 'true' &&
                $this->describe->hasColumn('is_active')) {
            return $this->deactivate($columns);
        }
        
        $pdostmt = $this->dataobject()->prepare(
                'DELETE FROM `' . $this->getTable() . '`' . $this->where($columns));
        foreach ($columns as $column) {
            $pdostmt->bindValue($column->getBindName(), $column->getValue(), $column->getDataType());
        }
        
        return $pdostmt->execute();
    }

    public function deactivate(array $columns)
    {
        $pdostmt = $this->select('*', $columns);
        $result = $pdostmt->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return false;
        }
        
        $result['is_active'] = 0;

        if ( ! isset($result[$this->primary])) {
            return false;
        }
            
        $id = $result[$this->primary];

        foreach ($this->describe->getColumns() as $column) {
            if ($column->isUnique()) {
                if ($column->isNumeric()) {
                    if ($column->getName() != $this->primary) {
                        $result[$column->getName()] = PHP_INT_MAX - $result[$column->getName()];
                    }
                } else {
                    $result[$column->getName()] = '[' . \uniqid() . '] ' . $result[$column->getName()];
                }
            }
        }

        if ( ! $this->describe->getColumn($this->primary)->isNumeric()) {
            $id = $this->dataobject()->quote($id);
        }

        return $this->update($result, array(), "$this->primary=$id");    
    }

    public function deleteBy(array $params)
    {
        $columns = [];
        foreach ($params as $key => $value) {
            if ($this->describe->hasColumn($key)) {
                $this->describe->setColumnValue($key, $value);
                $columns[] = $this->describe->getColumn($key);
            }
        }
        
        return $this->delete($columns);
    }
    
    public function deleteByID($value)
    {
        $id = $this->describe->getColumn($this->primary);
        $id->setValue($value);
        
        return $this->delete(array($id));
    }

    public function getFirst($condition = null)
    {
        $query = "SELECT * FROM `{$this->getTable()}`";        
        if (isset($condition)) {
            $query .= ' ' . $condition;
        }
        $query .= ' LIMIT 1';
        
        $pdostmt = $this->dataobject()->prepare($query);                
        $pdostmt->execute();
        if ($pdostmt->rowCount() == 1) {
            return $pdostmt->fetch(\PDO::FETCH_ASSOC);
        }
        
        return null;
    }

    public function getRows(array $condition = []) : array
    {
        if (empty($condition)) {
            $condition['ORDER BY'] = $this->primary;
        }
        
        return $this->getStatementRows($this->select('*', array(), $condition));
    }
    
    public function getStatementRows(\PDOStatement $pdostmt) : array
    {
        $rows = array();
        while ($data = $pdostmt->fetch(\PDO::FETCH_ASSOC)) {
            foreach ($this->describe->getColumns() as $column) {
                if (isset($data[$column->getName()])) {
                    $rows[$data[$this->primary]][$column->getName()] = $data[$column->getName()];
                } else {
                    $rows[$data[$this->primary]][$column->getName()] = $column->getDefault(null);
                }
            }
        }
        
        return $rows;
    }
    
    public function getBy(string $name, string $value)
    {
        if ($this->describe->hasColumn($name)) {
            $column = $this->describe->getColumn($name);
            $column->setValue($value);
            
            return $this->getRowBy(array($column));
        }
        
        return null;
    }

    public function getRowBy(array $columns)
    {
        $pdostmt = $this->select('*', $columns);
        if ($pdostmt->rowCount() == 1) {
            return $pdostmt->fetch(\PDO::FETCH_ASSOC);
        }
        
        return null;
    }

    public function getByID($value)
    {
        return $this->getBy($this->primary, $value);
    }

    public function checkBy(string $name, $value)
    {
        if ($this->describe->hasColumn($name)) {
            $column = $this->describe->getColumn($name);
            $column->setValue($value);
            $pdostmt = $this->select('*', array($column));
            
            return $pdostmt->rowCount();
        }
        
        return false;
    }

    public function checkByID($value)
    {
        return $this->checkBy($this->primary, $value);
    }
}
