<?php

/**
 * Generate Model code
 *
 * @category ZFScaffold
 * @package ZFScaffold_Generator
 * @subpackage Zend
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Generator_Zend_Model
    extends ZFTool_Generator_Abstract
        implements ZFTool_Generator_Interface
{

    private $_modelName;

    public function generate()
    {
        $modelsDirectory = $this->_getModulePath() . DIRECTORY_SEPARATOR . 'models';
        $moduleName      = ucfirst($this->_config->moduleName);
        $controllerName  = ucfirst($this->_config->tableName);

        $this->_modelName = $moduleName . '_Model_' . $controllerName;

        $modelClass = new Zend_CodeGenerator_Php_Class();
        $modelClass->setName($this->_modelName);
        $modelClass->setExtendedClass('ZFTool_Model_Abstract');
        $modelClass->setDocblock($this->_generateDocBlock());
        $modelClass->setProperties($this->_getModelProperties());
        $modelClass->setMethods($this->_getModelMethods());

        $this->_generateFile($modelClass, $modelsDirectory . DIRECTORY_SEPARATOR . $controllerName . '.php');
    }

    protected function _getModelProperties()
    {
        $variables = array();
        foreach ($this->_config as $field) {
            if (!isset($field->PRIMARY)) {
                continue;
            }

            $variables[] = array(
                'name'       => '_' . $field->COLUMN_NAME,
                'visibility' => 'private'
            );
        }
        return $variables;
    }

    protected function _getModelMethods()
    {
        $methods = array();
        foreach ($this->_config as $field) {
            if (!isset($field->PRIMARY)) {
                continue;
            }

            $methodName = ucfirst($field->COLUMN_NAME);
            $setParams = array(array('name' => 'field'));

            $methods[] = $this->_createMethod(
                    'set' . $methodName,
                    $setParams,
                    '$this->_' . $field->COLUMN_NAME . ' = $field;'
            );

            $methods[] = $this->_createMethod(
                    'get' . $methodName,
                    array(),
                    'return $this->_' . $field->COLUMN_NAME . ';'
            );
        }
        return $methods;
    }
}
