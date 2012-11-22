<?php

/**
 * Get db adapter from config
 *
 * @category ZFScaffold
 * @package ZFScaffold_Application
 * @subpackage Resource
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Application_Resource_Scaffold extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Adapter options
     *
     * @var array
     */
    protected $_adapterOptions = array();

    /**
     * Set adapter options
     *
     * @param array $options
     * @return void
     */
    public function setAdapter(array $options)
    {
        $this->_adapterOptions = $options['db_adapter'];
    }

    /**
     * Init resource
     *
     * @return array
     */
    public function init()
    {
        return $this->_adapterOptions;
    }
}