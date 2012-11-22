<?php
/**
 * Get Zend_Db_Adapter
 *
 * @category ZFScaffold
 * @package ZFScaffold_Adapter
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Adapter_Zend extends ZFTool_Adapter_Abstract
{

    protected $_adapterName = 'Zend';

    /**
     * Get columns from table use Zend_Db_Adapter
     *
     * @return array
     */
    public function getColumnsFromTable()
    {
        return Zend_Db_Table::getDefaultAdapter()->describeTable($this->_tableName);
    }

}
