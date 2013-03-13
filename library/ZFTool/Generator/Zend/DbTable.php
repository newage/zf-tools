<?php

/**
 * Generate model/DbTable
 *
 * @category Library
 * @package Library_Generator_Zend
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
 */
class ZFTool_Generator_Zend_DbTable
    extends ZFTool_Generator_Abstract
        implements ZFTool_Generator_Interface
{

    private $_dbTableName;

    /**
     * Generate models/DbTable extends Zend_Db_Table_Abstract
     *
     * @TODO Add description fields of table
     */
    public function generate()
    {
        $tableDirectory = $this->_getModulePath() . DIRECTORY_SEPARATOR . 'models' .
                DIRECTORY_SEPARATOR . 'DbTable';
        $moduleName     = ucfirst($this->_config->moduleName);
        $tableName      = ucfirst($this->_config->tableName);

        $this->_dbTableName = $moduleName . '_Model_DbTable_' . $tableName;

        $tableClass = new Zend_CodeGenerator_Php_Class();
        $tableClass->setName($this->_dbTableName);
        $tableClass->setExtendedClass('Zend_Db_Table_Abstract');

        $tableClass->setProperty(
            array(
                'name' => '_name',
                'visibility' => 'protected',
                'defaultValue' => $this->_config->tableName
            )
        );

        $this->_generateFile($tableClass, $tableDirectory . DIRECTORY_SEPARATOR . $tableName . '.php');
    }
}
