<?php

/**
 * Get db adapter from config
 *
 * @category Library
 * @package Library_Appliucation_Resource
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
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