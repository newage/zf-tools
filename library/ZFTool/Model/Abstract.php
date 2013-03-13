<?php
/**
 * Initialize set/get method in model
 *
 * @category Library
 * @package Library_Model
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
 */
abstract class ZFTool_Model_Abstract
{

    /**
     * Constructor
     * @param array $options [optional]
     */
    public function __construct($options = null)
    {
        if (null != $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options
     *
     * @param array $options
     * @return ZFTool_Db_Table_Row_Abstract
     */
    public function setOptions($options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Check and init set method on value
     *
     * @param string $columnName
     * @param string $value
     * @return string|bool
     */
    public function  __set($columnName, $value)
    {
        $methodName = 'set' . ucfirst($columnName);

        if (method_exists($this, $methodName)) {
            return $this->$methodName($value);
        }
        return false;
    }

    /**
     * Check and init get method for value
     *
     * @param string $columnName
     * @return string|bool
     */
    public function __get($columnName)
    {
        $methodName = 'get' . ucfirst($columnName);

        if (method_exists($this, $methodName)) {
            return $methodName();
        }
        return false;
    }
}
