<?php

namespace codesaur\DataObject;

use PDO;

class Column
{
    private $_name;
    private $_type;
    private $_length;
    private $_default = null;

    private $_post_name;
    private $_post_type;
    private $_input_type;
    
    private $_is_null = true;
    private $_is_auto = false;
    private $_is_unique = false;
    private $_is_primary = false;
    
    private $_foreign_key = null;

    public $value = '';

    function __construct(
            string $name,
            string $type = 'int',
                   $length = 11,
                   $default = null
    ) {
        $this->setName($name);
        $this->setType($type);
        $this->setLength($length);
        $this->setDefault($default);
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function getSQL(string $collation): string
    {
        $str = "`$this->_name` $this->_type";
        
        if ($this->needLength()) {
            if (is_array($this->_length)) {
                if (isset($this->_length['M'])) {
                    $str .= "({$this->_length['M']}";
                    if (isset($this->_length['D'])) {
                        $str .= ",{$this->_length['D']}";
                    }
                    $str .= ')';
                }
            } else {
                $str .= "($this->_length)";
            }            
        }
        
        if ($this->needCollate()) {
            $str .= " COLLATE $collation";
        }
        
        $default = ' DEFAULT ';
        if ($this->_default !== null) {
            if ($this->isIntType()) {
                $default .= $this->_default;
            } else {
                $default .= "'$this->_default'";
            }
        } else {
            $default .= 'NULL';
        }
        
        if (!$this->isNull()) {
            $str .= ' NOT NULL';
            if ($this->_default !== null) {
                $str .= $default;
            }
        } else {
            $str .= $default;
        }
        
        if ($this->isAuto()) {
            $str .= ' AUTO_INCREMENT';
        }
        
        return $str;
    }

    final public function getBindName(): string
    {
        return ':' . $this->getName();
    }

    public function getDataType(): int
    {
        switch ($this->getType()) {
            case 'int':
            case 'tinyint':
            case 'bigint': return PDO::PARAM_INT;
            
            default: return PDO::PARAM_STR;
        }
    }

    public function getDefault()
    {
        return $this->_default;
    }

    public function getFilter(): int
    {
        switch ($this->getPostType()) {
            case 1:  {
                if (is_array($this->_length)) {
                    return FILTER_VALIDATE_FLOAT;
                } else {
                    return FILTER_VALIDATE_INT;
                }
            } break;
            
            case 4: return FILTER_VALIDATE_EMAIL;
            
            default: return FILTER_DEFAULT;
        }
    }

    public function getInputType(): string
    {
        return $this->_input_type;
    }

    public function getLength()
    {
        if ($this->isUnique()
                && $this->getType() === 'varchar') {
            return $this->_length - 15;
        }
        
        return $this->_length;
    }

    final public function getName(): string
    {
        return $this->_name;
    }

    public function getPost()
    {
        if (is_array($_POST[$this->getPostName()])) {
            return filter_input(INPUT_POST, $this->getPostName(), $this->getFilter(), FILTER_REQUIRE_ARRAY);
        } elseif ($this->isIntType() 
                && 'on' == filter_input(INPUT_POST, $this->getPostName())) {
            return 1;
        }
        
        return filter_input(INPUT_POST, $this->getPostName(), $this->getFilter());
    }

    public function getPostName(?string $key = null): string
    {
        $postname = $this->_post_name;
        
        if (empty($key)) {
            $postname .= "[$key]";
        }
        
        return $postname;
    }

    public function getPostType(): int
    {
        // 0 - string
        // 1 - int
        // 2 - files
        // 3 - password
        // 4 - email
        // 5 - text
        return $this->_post_type;
         
    }

    public function getType(): string
    {
        return $this->_type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function hasPost(): bool
    {
        return filter_has_var(INPUT_POST, $this->getPostName());
    }

    public function isAuto(): bool
    {
        return $this->_is_auto;
    }

    public function isIntType(): bool
    {
        return $this->getType() === 'int'
                || $this->getType() === 'tinyint'
                || $this->getType() === 'bigint';
    }

    public function isNull(): bool
    {
        return $this->_is_null;
    }

    public function isNumeric(): bool
    {
        return $this->getPostType() === 1;
    }

    public function isPrimary(): bool
    {
        return $this->_is_primary;
    }

    public function isUnique(): bool
    {
        return $this->_is_unique;
    }
    
    public function hasForeignKey(): bool
    {
        return isset($this->_foreign_key);
    }
    
    public function getForeignKey()
    {
        return $this->_foreign_key;
    }

    public function auto(bool $auto = true): Column
    {
        $this->_is_auto = $auto;

        return $this;
    }

    public function unique(bool $unique = true): Column
    {
        $this->_is_unique = $unique;

        return $this;
    }

    public function primary(bool $primary = true): Column
    {
        $this->_is_primary = $primary;
        
        return $this;
    }

    public function notNull(bool $not_null = true): Column
    {
        $this->_is_null = !$not_null;
        
        return $this;
    }

    public function foreignKey(string $references, $name = null): Column
    {
        if (isset($name)) {
            $this->_foreign_key = array($name => $references);
        } else {
            $this->_foreign_key = $references;
        }
        
        return $this;
    }
    
    public function needCollate(): bool
    {
        return in_array($this->_type, array('varchar', 'text'));
    }

    public function needLength(): bool
    {
        return !in_array($this->_type, array('text', 'datetime'));
    }

    public function setDefault($default)
    {
        $this->_default = $default;
    }

    public function setInputType(string $input_type)
    {
        $this->_input_type = $input_type;
    }

    public function setLength($length)
    {
        if (is_float($length)) {
            $this->_length = array(
                'M' => (int)$length,
                'D' => (int)(($length - $this->_length['M']) * 10)
            );
        } else {
            $this->_length = $length;
        }
    }

    final public function setName(string $name)
    {
        $this->_name = $name;
        $this->setPostName("txt_$name");
    }
    
    final public function setPostName(string $post_name)
    {
        $this->_post_name = $post_name;
    }

    public function setPostType(int $post_type): Column
    {
        $this->_post_type = $post_type;
        switch ($post_type) {
            case 1: $this->setInputType('number'); break;
            case 2: $this->setInputType('file'); break;
            case 3: $this->setInputType('password'); break;
            case 4: $this->setInputType('email'); break;
            case 6: $this->setInputType('radio'); break;
            case 7: $this->setInputType('checkbox'); break;
            
            default: $this->setInputType('text'); break;
        }
        
        return $this;
    }

    public function setType(string $type): Column
    {
        $this->_type = $type;
        switch ($type) {
            case 'int':
            case 'tinyint':
            case 'bigint': 
            case 'decimal': $this->setPostType(1); break;
            
            default: $this->setPostType(0); break;
        }
        
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}
