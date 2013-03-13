<?php
/**
 * Get Zend_Db_Adapter
 *
 * @category Library
 * @package Library_Adapter
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
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
