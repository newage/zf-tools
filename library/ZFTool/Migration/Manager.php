<?php
/**
 * Class Core_Migration_Manager
 *
 * Migration manager
 *
 * @category Core
 * @package  Core_Migration
 * @author   V.Leontiev
 * 
 * @version  $Id$
 */
class Core_Migration_Manager
{
    /**
     * Variable contents options
     *
     * @var array
     */
    protected $_options = array(
        // Migrations schema table name
        'migrationsSchemaTable'   => 'migrations',
    );
    
    /**
     * Message stack
     * 
     * @var array 
     */
    protected $_messages   = array();
    
    /**
     * Constructor of Core_Migration_Manager
     *
     * @access  public
     * @param   array $options
     */
    public function __construct($options = array()) 
    {
        if ($options) {
            $this->_options = array_merge($this->_options, $options);
        }
        
        $this->_init();
    }
    
    /**
     * Method initialize migration schema table
     */
    protected function _init()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `".$this->getMigrationsSchemaTable()."`(
                `migration` int NOT NULL
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";
        Zend_Db_Table::getDefaultAdapter()->query($sql);
    }
    
    /**
     * Method returns path to migrations directory
     * 
     * @return string 
     */
    public function getMigrationsDirectoryPath()
    {
        $path = APPLICATION_PATH . '/configs/migrations';
        
        $this->_preparePath($path);
        
        return $path;
    }
    
    /**
     * Method prepare path (create not existing dirs)
     * 
     * @param string $path 
     */
    protected function _preparePath($path)
    {
        if (!is_dir($path)) {
            $this->_preparePath(dirname($path));
            mkdir($path, 0777);
        }
    }
    
    /**
     * Method return migrations schema table
     *  
     * @return string
     */
    public function getMigrationsSchemaTable()
    {
        return $this->_options['migrationsSchemaTable'];
    }
    
    /**
     * Method returns array of exists in filesystem migrations
     *
     * @return array
     */
    public function getExistsMigrations() 
    {
        $filesDirty = scandir($this->getMigrationsDirectoryPath());

        $files = array();
        for ($i=0; $i<count($filesDirty); $i++) {
            if (substr($filesDirty[$i], 0, 1) != '.') {
                array_push($files, substr($filesDirty[$i], 0, -4));
            }
        }

        return $files;
    }
    
    /**
     * Method returns stack of messages
     *
     * @return array
     */
    public function getMessages() 
    {
        return $this->_messages;
    }
    
    /**
     * Method return number of save migrations
     *
     * @return string
     */
    public function getLastMigration() 
    {
        $lastMigration = null;
        
        try {
            $select = Zend_Db_Table::getDefaultAdapter()->select()
                ->from($this->getMigrationsSchemaTable(), array('migration'));

            $lastMigration = Zend_Db_Table::getDefaultAdapter()->fetchOne($select);

        } catch (Exception $e) {
            // maybe table is not exist; this is first revision
            $lastMigration = 0;
        }
        
        //Insert new row
        if ($lastMigration === false) {
            $sql = "INSERT INTO `".$this->getMigrationsSchemaTable()."` VALUES(0)";
            Zend_Db_Table::getDefaultAdapter()->query($sql);
            
            $lastMigration = 0;
        }
        
        return $lastMigration;
    }
    
    /**
     * Method create's new migration file
     * 
     * @return string Migration name
     */
    public function create()
    {
        $path = $this->getMigrationsDirectoryPath();
        $_migrationName = time();
       
        // Configuring after instantiation
        $methodUpDoc = new Zend_CodeGenerator_Php_Docblock(array(
            'setShortDescription' => 'Upgrade method',
            'tags' => array(
                array(
                    'name' => 'author',
                    'description' => ''
                ),
                array(
                    'name' => 'return',
                    'description' => 'bool'
                )
            )
        ));
        
        $methodUp = new Zend_CodeGenerator_Php_Method();
        $methodUp->setName('up')
                 ->setDocblock($methodUpDoc)
                 ->setBody('$this->query("");');
                 
        // Configuring after instantiation
        $methodDownDoc = new Zend_CodeGenerator_Php_Docblock(array(
            'setShortDescription' => 'Downgrade method',
            'tags' => array(
                array(
                    'name' => 'author',
                    'description' => ''
                ),
                array(
                    'name' => 'return',
                    'description' => 'bool'
                )
            )
        ));
        
        $methodDown = new Zend_CodeGenerator_Php_Method();
        $methodDown->setName('down')
                   ->setDocblock($methodDownDoc)
                   ->setBody('$this->query("");');
                   
        $class = new Zend_CodeGenerator_Php_Class();
        $className = 'Migration_' . $_migrationName;

        $classDoc = new Zend_CodeGenerator_Php_Docblock(array(
            'setShortDescription' => 'Autogenerate migration class',
            'tags' => array(
                array(
                    'name' => 'category',
                    'description' => 'Application'
                ),
                array(
                    'name' => 'package',
                    'description' => 'Application_Migration',
                ),
                array(
                    'name' => 'author'
                ),
                array(
                    'name'        => 'license',
                    'description' => 'New BSD',
                ),
                array(
                    'name' => 'version',
                    'description' => '$Id$'
                )
            )
        ));
        
        $class->setName($className)
              ->setExtendedClass('Core_Migration_Abstract')
              ->setMethod($methodUp)
              ->setMethod($methodDown)
              ->setDocblock($classDoc);
        
        $file = new Zend_CodeGenerator_Php_File();
        $file->setClass($class)
             ->setFilename($path . '/' . $_migrationName . '.php')
             ->write();
             
        return $_migrationName;
    }
    
    /**
     * Method upgrade all migration or migrations to selected
     *
     * @param string $tomigration
     */
    public function migration($toMigration) 
    {
        $applyMigration = $this->getLastMigration();
        
        if ($toMigration == 0 || $toMigration == 'last' || $toMigration > $applyMigration) {
            //upgrade
            $upgrade = true;
        } elseif ($toMigration < $applyMigration) {
            //downgrade
            $upgrade = false;
        }
        
        $applyMigrationFiles = $this->_getApplyMigrationFiles($toMigration, $upgrade);
        $this->applyMigrationFiles($applyMigrationFiles, $upgrade);
    }

    /**
     * Execute migration files
     * 
     * @param array $files
     * @param bool $upgrade 
     */
    protected function applyMigrationFiles(array $files, $upgrade = true)
    {
        $applyMigration = $this->getLastMigration();
        
        foreach ($files as $migration) {
            $includePath = $this->getMigrationsDirectoryPath() .
                DIRECTORY_SEPARATOR . $migration . '.php';

            include_once $includePath;

            $methodName = $upgrade === true ? 'up' : 'down';
            $migrationClass  = 'Migration_'.$migration;
            $migrationObject = new $migrationClass;  

            $migrationObject->getDbAdapter()->beginTransaction();
            
            try {
                $migrationObject->$methodName();
                $migrationObject->getDbAdapter()->commit();
                if ($upgrade === true) {
                    ++$applyMigration;
                    $this->addMessage('Upgrade revision ' . $applyMigration . '. #' . $migration, 'green');
                } else {
                    $this->addMessage('Downgrade revision ' . $applyMigration . '. #' . $migration, 'green');
                    --$applyMigration;
                }
                //update migration version
                $this->_updateMigration($applyMigration);
            } catch (Exception $e) {
                $migrationObject->getDbAdapter()->rollBack();
                $this->_updateMigration($applyMigration);
                $this->addMessage('Commit failed of migration "' . $migration . '"', 'red');
                $this->addMessage($e->getMessage(), 'red');
                break;
            }
        }
    }
    
    /**
     * Get files to apply migration
     * 
     * @param int $maxMigration 
     * @param bool $upgrade
     * @return bool|array
     */
    protected function _getApplyMigrationFiles($toMigration, $upgrade = true)
    {
        $existFiles = $this->getExistsMigrations();
        $applyMigration = (int)$this->getLastMigration();
        $fileCounter = count($existFiles);
        
        if ($toMigration == 0) {
            $toMigration = $fileCounter;
        }
        
        if ($toMigration > $applyMigration) {
            sort($existFiles);
            $applyMigrationFiles = array_slice($existFiles, $applyMigration, $toMigration);
            return $applyMigrationFiles;
            
        } elseif ($toMigration < $applyMigration) {
            $applyMigrationFiles = array_slice($existFiles, $toMigration, $applyMigration - $toMigration);
            rsort($applyMigrationFiles);
            return $applyMigrationFiles;
            
        } else {
            $this->addMessage('Database contains the latest changes', 'green');
            return true;
        }

    }
    
    /**
     * Method add migration to schema table
     * 
     * @param string $migration Migration name
     * @return Core_Migration_Manager 
     */
    protected function _updateMigration($migration)
    {
        try {
            $sql = "UPDATE `".$this->getMigrationsSchemaTable()."` SET migration = ?";
            Zend_Db_Table::getDefaultAdapter()
                ->query($sql, array($migration));
        } catch (Exception $e) {
            // table is not exist
        }
        
        return $this;
    }
   
    /**
     * Add new message
     * 
     * @param string $message
     * @param array $color
     */
    public function addMessage($message, $color = null)
    {
        $this->_messages[] = array(
            'message' => $message,
            'color' => $color
        );
    }
    
    /**
     * Get all messages
     * 
     * @return array
     */
    public function getMessage()
    {
        return $this->_messages;
    }
}