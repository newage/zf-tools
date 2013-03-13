<?php

/**
 * Database manager tool
 *
 * @category Library
 * @package Library_Tool_Project_Provider
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
 */
class ZFTool_Tool_Project_Provider_Schema
    extends ZFTool_Tool_Project_Provider_Abstract
        implements Zend_Tool_Framework_Provider_Pretendable
{

    protected $_title = '[Schema]';

    /**
     * Drop all tables from current schema
     * And load dump sql
     *
     * @author V.Leontiev
     */
    public function apply($dumpFile = 'clean')
    {
        $resources = $this->_app->getOption('resources');
        $schemaName = $resources['db']['params']['dbname'];

        $sql = 'DROP SCHEMA `' . $schemaName . '`';
        $message = 'Drop current schema: ' . $schemaName;
        $this->_applyQuery($sql, $message);

        $sql = 'CREATE SCHEMA `' . $schemaName . '`';
        $message = 'Create current schema: ' . $schemaName;
        $this->_applyQuery($sql, $message);

        $sql = 'USE `' . $schemaName . '`';
        $this->_applyQuery($sql);

        $this->_applyDumpFile($dumpFile);
    }

    /**
     * Apply dump file to current schema
     *
     * @param string $dumpFile
     * @author V.Leontiev
     */
    protected function _applyDumpFile($dumpFile)
    {
        $dir = 'application/configs/sql';
        $file = $dumpFile . '.sql';
        $pathToFile = $dir . DIRECTORY_SEPARATOR . $file;

        if (file_exists($pathToFile)) {

            $sqlFileContent = explode(';', file_get_contents($pathToFile));
            $sqlArrayCount = count($sqlFileContent);

            for ($i = 0; $i < $sqlArrayCount; $i++) {
                $sql = trim($sqlFileContent[$i]);
                if (empty ($sql)) {
                    continue;
                }
                Zend_Db_Table::getDefaultAdapter()->query($sql);
            }

            $this->_print('Apply file: ' . $file, array('color' => 'green'));
        } else {
            $this->_print('Dont exists file: ' . $pathToFile, array('color' => 'red'));
        }

    }

    /**
     * Sql dump for current schema
     *
     * @param type $schemaName
     */
    public function dump()
    {
        $file = uniqid() . '.sql';
        $this->_print('Create dump file: ' . $file, array('color' => 'green'));
    }

    /**
     * Apply query
     *
     * @author V.Leontiev
     * @param string $sql
     * @param string $message
     */
    protected function _applyQuery($sql, $message = null)
    {
        try {
            Zend_Db_Table::getDefaultAdapter()->query($sql);
            if ($message !== null) {
                $this->_print($message, array('color'=>'green'));
            }
        } catch (Exception $e) {
            $this->_print('Error: ' . $e, array('color'=>'red'));
        }
    }
}

