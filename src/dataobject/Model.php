<?php

namespace codesaur\DataObject;

class Model extends Table
{
    public function insert(array $record)
    {
        if ($this->describe->hasColumn('created_at')
                && !isset($record['created_at'])) {
            $record['created_at'] = date('Y-m-d H:i:s');
        }

        if ($this->describe->hasColumn('created_by')
                && !isset($record['created_by'])
                && getenv('CODESAUR_ACCOUNT_ID', true)) {
            $record['created_by'] = (int)getenv('CODESAUR_ACCOUNT_ID', true);
        }

        return parent::insert($record);
    }
    
    public function update(
            array  $record,
            array  $where = [],
            string $condition = ''
    ) {
        if ($this->describe->hasColumn('updated_at')
                && !isset($record['updated_at'])) {
            $record['updated_at'] = date('Y-m-d H:i:s');
        }
        
        if ($this->describe->hasColumn('updated_by')
                && !isset($record['updated_by'])
                && getenv('CODESAUR_ACCOUNT_ID', true)) {
            $record['updated_by'] = (int)getenv('CODESAUR_ACCOUNT_ID', true);
        }
        
        return parent::update($record, $where, $condition);
    }   
    
    public function getByKeyword(string $value)
    {
        return $this->getBy('_keyword_', $value);
    }
}
