<?php

/**
 * Generate ModelMapper code.
 * Use pattern DataMapper.
 * Generate CRUD bussines-logic
 *
 * @category ZFScaffold
 * @package ZFScaffold_Generator
 * @subpackage Zend
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Generator_Zend_ModelMapper
    extends ZFTool_Generator_Abstract
        implements ZFTool_Generator_Interface
{

    public function generate()
    {
        $modelsDirectory = $this->_getModulePath() . DIRECTORY_SEPARATOR . 'models';
        $moduleName      = ucfirst($this->_config->moduleName);
        $tableName       = ucfirst($this->_config->tableName);
        $modelName       = $moduleName . '_Model_' . $tableName;
        $mapperName      = $modelName . 'Mapper';
        $dbTable         = $moduleName . '_Model_DbTable_' . $tableName;
        $methodParams    = array(array('name'=>'data', 'type'=>$modelName));

        $modelClass = new Zend_CodeGenerator_Php_Class();
        $modelClass->setName($mapperName);
        $modelClass->setDocblock($this->_generateDocBlock());
        $modelClass->setExtendedClass('ZFTool_Model_Mapper_Abstract');
        $modelClass->setProperty(array(
            'name'         => '_dbName',
            'visibility'   => 'protected',
            'defaultValue' => $dbTable
         ));
        $modelClass->setDocblock($this->_generateDocBlock());
        $modelClass->setMethod($this->_createMethod('create', $methodParams));
        $modelClass->setMethod($this->_createMethod('update', $methodParams));
        $modelClass->setMethod($this->_createMethod('delete', $methodParams));

        $this->_generateFile($modelClass, $modelsDirectory . DIRECTORY_SEPARATOR . $tableName . 'Mapper.php');
    }

    protected function _getBodyForDelete()
    {
        $body = 'return $this->getDbTable()->find($data->getId())->current()->delete();';
        return $body;
    }

    protected function _getBodyForCreate()
    {
        $body = array();
        $body[] = '$newRow = $this->getDbTable()->createRow();';

        foreach ($this->_config as $field) {
            if (!isset($field->PRIMARY) || true === $field->PRIMARY) {
                continue;
            }
            $body[] = '$newRow->' . $field->COLUMN_NAME . ' = $data->get' . ucfirst($field->COLUMN_NAME). '();';
        }

        $body[] = 'return $newRow->save();';

        return implode("\n", $body);
    }

    protected function _getBodyForUpdate()
    {
        $body = array();
        $body[] = '$row = array(';

        foreach ($this->_config as $field) {
            if (!isset($field->PRIMARY) || true === $field->PRIMARY) {
                continue;
            }
            $body[] = '  \'' . $field->COLUMN_NAME . '\' => $data->get' . ucfirst($field->COLUMN_NAME) . '(),';
        }
        $body[] = ');';

        $body[] = 'return $this->getDbTable()->update($row, \'id = \' . $data->getId());';

        return implode("\n", $body);
    }
}
