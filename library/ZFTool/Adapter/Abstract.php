<?php
/**
 * Abstract adapter
 *
 * @category Library
 * @package Library_Adapter
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
 */
abstract class ZFTool_Adapter_Abstract
{

    /**
     * Adapter name
     *
     * @var string
     */
    protected $_adapterName;

    /**
     * Table name
     *
     * @var string
     */
    protected $_tableName;

    /**
     * Constructor
     *
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        $this->_tableName = $tableName;
    }

    /**
     * Get dbAdapter
     *
     * @return Zend_Db_Adapter
     */
    abstract public function getColumnsFromTable();
}

