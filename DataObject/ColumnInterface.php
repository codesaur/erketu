<?php namespace codesaur\DataObject;

interface ColumnInterface
{
    function __construct(
            string $name,
            string $type = 'int',
            $length = 11, $default = null);
        
    public function getSQL(string $collation) : string;
    
    public function getBindName() : string;    
    public function getDataType() : int;
    public function getDefault($thenull = '');
    public function getFilter() : int;
    public function getInputType() : string;
    public function getLength();
    public function getName() : string;
    public function getPost();
    public function getPostName(string $key = null) : string;
    public function getPostType() : int;
    public function getType() : string;
    public function getValue();
    
    public function hasPost() : bool;
    
    public function isAuto() : bool;
    public function isIntType() : bool;
    public function isNull() : bool;
    public function isNumeric() : bool;
    public function isPrimary() : bool;
    public function isUnique() : bool;
    
    public function auto(bool $auto = true) : Column;
    public function unique(bool $unique = true) : Column;
    public function primary(bool $primary = true) : Column;
    public function notNull(bool $not_null = true) : Column;
    
    public function needCollate() : bool;
    public function needLength() : bool;
    
    public function setDefault($default);
    public function setInputType(string $input_type);
    public function setLength($length);
    public function setName(string $name);
    public function setPostName(string $post_name);
    public function setPostType(int $post_type) : Column;
    public function setType(string $type) : Column;
    public function setValue($value);
}
