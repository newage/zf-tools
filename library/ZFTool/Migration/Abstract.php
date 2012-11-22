<?php

/**
 * Class Core_Migration_Abstract
 *
 * abstract migration
 *
 * @category Core
 * @package  Core_Migration
 * @author   V.Leontiev
 * 
 * @version  $Id$
 */
abstract class Core_Migration_Abstract
{
    const TYPE_INT = 'int';
    const TYPE_BIGINT = 'bigint';
    
    const TYPE_FLOAT = 'float';
    
    const TYPE_TEXT = 'text';
    const TYPE_LONGTEXT = 'longtext';
    
    const TYPE_VARCHAR = 'varchar';
    const TYPE_ENUM = 'enum';
    
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIME = 'time';
    const TYPE_TIMESTAMP = 'timestamp';    
    
    /**
     * Default Database adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbAdapter = null;
    
    /**
     * migration Adapter
     *
     * @var Core_Migration_Adapter_Abstract
     */
    
    protected $_migrationAdapter = null;
    
    /**
     * up
     *
     * update DB from migration
     * 
     * @author V.Leontiev
     * @return  Core_Migration_Abstract
     */
    abstract public function up();
    
    /**
     * down
     *
     * degrade DB from migration
     * 
     * @author V.Leontiev
     * @return  Core_Migration_Abstract
     */
    abstract public function down();

    /**
     * setDbAdapter
     *
     * @author V.Leontiev
     * @param  Zend_Db_Adapter_Abstract $dbAdapter
     * @return Core_Migration_Abstract
     */
    function setDbAdapter($dbAdapter = null)
    {
        if ($dbAdapter && ($dbAdapter instanceof Zend_Db_Adapter_Abstract)) {
            $this->_dbAdapter = $dbAdapter;
        } else {
            $this->_dbAdapter = Zend_Db_Table::getDefaultAdapter();
        }
        return $this;
    }
    /**
     * getDbAdapter
     *
     * @author V.Leontiev
     * @return setDbAdapter
     */
    function getDbAdapter()
    {
        if (!$this->_dbAdapter) {
            $this->setDbAdapter();
        }
        return $this->_dbAdapter;
    } 
     
    /**
     * stop
     *
     * @throws Exception
     */
    public function stop() 
    {
        throw new Zend_Exception('This is final migration');
    }
       
    /**
     * query
     *
     * @param   string     $query
     * @return  Zend_Db_Statement_Interface
     */
    public function query($query) 
    {
        $this->getDbAdapter()->query($query);
        return $this;
    }
    
    /**
     * insert
     *
     * @param   string     $table
     * @param   array      $params
     * @return  int The number of affected rows.
     */
    public function insert($table, array $params) 
    {
        return $this->getDbAdapter()->insert($table, $params);
    }
    
    /**
     * Updates table rows with specified data based on a WHERE clause.
     *
     * @param  mixed        $table The table to update.
     * @param  array        $bind  Column-value pairs.
     * @param  mixed        $where UPDATE WHERE clause(s).
     * @return int          The number of affected rows.
     */
    public function update($table, array $bind, $where = '')
    {
        return $this->getDbAdapter()->update($table, $bind, $where);
    }
    
    /**
     * Delete table rows with specified data based on a WHERE clause.
     *
     * @param  mixed        $table The table to update.
     * @param  mixed        $where UPDATE WHERE clause(s).
     * @return int          The number of affected rows.
     */
    public function delete($table, $where = '')
    {
        $this->getDbAdapter()->delete($table, $where);
        return $this;
    }
     
    /**
     * createTable
     *
     * @param   string $table table name
     * @return  Core_Migration_Abstract
     */
    public function createTable($table) 
    {
        $this->query(
            'CREATE TABLE '.
            $table.
            ' ( `id` bigint NOT NULL AUTO_INCREMENT , PRIMARY KEY (`id`))'.
            ' Engine=InnoDB'
        );
        
        return $this;
    }
    
    /**
     * dropTable
     *
     * @param   string     $table  table name
     * @return  Core_Migration_Abstract
     */
    public function dropTable($table) 
    {
        return $this;
    }
    
    /**
     * createColumn
     *
     * Not realise
     * 
     * @param   string   $table
     * @param   string   $column 
     * @param   string   $datatype
     * @param   string   $length
     * @param   string   $default
     * @param   bool     $notnull
     * @param   bool     $primary
     * @return  Core_Migration_Abstract
     */
    public function createColumn($table, 
                                 $column,
                                 $datatype,
                                 $length = null,
                                 $default = null,
                                 $notnull = false,
                                 $primary = false
                                 ) 
    {
        
    }
    
    /**
     * dropColumn
     * Not realise
     *
     * @param   string   $table
     * @param   string   $name 
     * @return  bool
     */
    public function dropColumn($table, $name) 
    {
        $this->getDbAdapter()->dropColumn($table, $name);
        
        return $this;
    }   

    /**
     * createUniqueIndexes
     * Not realise
     *
     * @param   string   $table
     * @param   array    $columns 
     * @param   string   $indName 
     * @return  bool
     */
    public function createUniqueIndexes($table, array $columns, $indName = null)
    {
        return $this;
    }    
    
    /**
     * dropColumn
     * Not realise
     *
     * @param   string   $table
     * @param   array    $columns
     * @return  bool
     */
    public function dropUniqueIndexes($table, $indName) 
    {
        return $this;
    }
    
    /**
     * message
     *
     * output message to console
     *
     * @param   string     $message
     * @return  Core_Migration_Abstract
     */
    public function message($message) 
    {
        echo $message . "\n";
        return $this;
    }
}