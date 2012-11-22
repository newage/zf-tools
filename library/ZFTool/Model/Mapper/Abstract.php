<?php
/**
 * Abstract Model mapper
 *
 * @category ZFScaffold
 * @package ZFScaffold_Model
 * @subpackage Mapper
 * @subpackage Resource
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
abstract class ZFTool_Model_Mapper_Abstract
{
    /**
     *
     * @var Zend_Db_Table_Abstract|String
     */
    protected $_dbTable = null;

    /**
     * Name of dbTable
     * @var string
     */
    protected $_dbName = null;

    /**
     *
     * @var Zend_Db_Table_Select
     */
    protected $_select = null;

    /**
     * Constructor
     * Initialize select from this table
     *
     */
    public function  __construct()
    {
        $this->_select = $this->getDbTable()->select(true);
    }

    /**
     * Add where rule to current select
     *
     * @param string $name
     * @param string $argument
     * @return ZFTool_Model_Mapper_Abstract|Zend_Db_Table_Rowset
     */
    public function __call($name, $argument)
    {
        if (substr($name, 0, 5) == 'fetch') {
            return $this->getDbTable()->$name($this->_select);
        } else {
            $this->_select->where($name . ' = ?', $argument[0]);
            return $this;
        }
    }

    /**
     * Set new dbTable
     * @param string $dbTable dbTable name
     * @return Zend_Db_Table_Abstract
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }

        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Zend_Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Get dbTable object
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable($this->_dbName);
        }
        return $this->_dbTable;
    }
}
