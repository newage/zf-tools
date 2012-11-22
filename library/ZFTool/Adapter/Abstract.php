<?php
/**
 * Abstract adapter
 *
 * @category ZFScaffold
 * @package ZFScaffold_Adapter
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
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

