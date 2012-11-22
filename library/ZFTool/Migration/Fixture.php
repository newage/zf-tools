<?php

/**
 * Load and save for fixtures files
 *
 * @category Core
 * @package  Core_Migration
 * @author   V.Leontiev
 * 
 * @version  $Id$
 */
class Core_Migration_Fixture
{
    
    /**
     * Load recors from fixture to base
     * 
     * @param string $fixtureName If null then load all files from fixture folder
     * @author V.Leontiev
     */
    public function load($fixtureName)
    {
        if ($fixtureName === null) {
            $fixtureName = $this->_scanFixtureDirectory();
        } else {
            $fixtureName = array($fixtureName);
        }
        
        foreach ($fixtureName as $fixtureOne) {
            $pathToFile = $this->getFixturesDirectoryPath() . DIRECTORY_SEPARATOR . $fixtureOne;

            if (file_exists($pathToFile)) {
                $fixture = $this->_getContentFromReader($pathToFile);

                foreach ($fixture as $table => $rowset) {
                    $this->_setDataToTable($table, $rowset);
                }
                $this->addMessage('Add data from fixture: ' . $fixtureOne, 'green');
            } else {
                $this->addMessage('Dont exists file: ' . $pathToFile, 'red');
            }
        }
    }
    
    /**
     * Save records from table to fixture file
     * 
     * @param string $tableName
     */
    public function save($tableName)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from($tableName);
        $rows = $db->fetchAll($select);

        $yaml = Core_Migration_Writer_Yml::encode(array($tableName => $rows));

        $fileName = $tableName . '_dump.yml';
        $pathToFile = $this->getFixturesDirectoryPath() . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($pathToFile, $yaml);
        $this->addMessage('Records from table "' . $tableName . '" to fixture "' . $fileName . '"', 'green');
    }
    
    /**
     * Create reader
     * 
     * @param string $filePath
     * @param string $readerName
     * @return Core_Migration_Reader_Abstract
     * @todo add object validation 
     */
    protected function _getContentFromReader($filePath)
    {
        $pathInfo = pathinfo($filePath);
        $objectName = 'Core_Migration_Reader_' . ucfirst($pathInfo['extension']);
        return new $objectName($filePath);
    }
    
    /**
     * Add data to table
     * 
     * @param string $tableName
     * @param string $data 
     */
    protected function _setDataToTable($tableName, $rowset)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        foreach ($rowset as $row){
            if ($row instanceof Core_Migration_Reader_Abstract) {
                $db->insert($tableName, $row->toArray());
            } else {
                throw new Core_Migration_Exception('Row not instance Core_Migration_Reader_Abstract');
            }
        }
    }
    
    /**
     * Get all files name from fixture directory
     * 
     * @return array
     */
    protected function _scanFixtureDirectory()
    {
        $files = scandir($this->getFixturesDirectoryPath());
        unset($files[0]);
        unset($files[1]);
        return $files;
    }
    
    /**
     * Method returns path to fixtures directory
     * 
     * @return string 
     */
    public function getFixturesDirectoryPath()
    {
        $path = APPLICATION_PATH . '/configs/fixtures';
        return $path;
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
     * Method returns stack of messages
     *
     * @return array
     */
    public function getMessages() 
    {
        return $this->_messages;
    }
}

