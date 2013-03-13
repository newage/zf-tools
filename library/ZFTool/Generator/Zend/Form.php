<?php

/**
 * Generate form
 *
 * @category Library
 * @package Library_Generator_Zend
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
 */
class ZFTool_Generator_Zend_Form
    extends ZFTool_Generator_Abstract
        implements ZFTool_Generator_Interface
{

    /**
     * Class form name
     * @var string
     */
    private $_formName;

    /**
     * Form element order counter
     * @var int
     */
    private static $_elementOrder = 1;

    /**
     * Generate form file
     */
    public function generate()
    {
        $formDirectory    = $this->_getModulePath() . DIRECTORY_SEPARATOR . 'forms';
        $moduleName       = ucfirst($this->_config->moduleName);
        $controllerName   = ucfirst($this->_config->tableName);
        $this->_formName  = $moduleName . '_Form_' . $controllerName;
        $this->_modelName = $moduleName . '_Model_' . $controllerName;

        $formClass = new Zend_CodeGenerator_Php_Class();
        $formClass->setName($this->_formName);
        $formClass->setExtendedClass('Zend_Form');
        $formClass->setDocblock($this->_generateDocBlock());

        $formClass->setMethod($this->_createMethod('init'));

        $params = array(array('name' => 'record', 'type' => 'Zend_Db_Table_Row'));
        $formClass->setMethod($this->_createMethod('update', $params));

        $this->_generateFile($formClass, $formDirectory . DIRECTORY_SEPARATOR . $controllerName . '.php');
    }

    /**
     * Generate update method
     *
     * @return string
     */
    protected function _getBodyForUpdate()
    {
        $body = '
foreach($this->getElements() as $key => $element ) {
    if (isset($record->$key)) {
        $element->setValue($record->$key);
    }
}
$this->getElement(\'submit\')->setLabel(\'Update\');';

        return $body;
    }

    /**
     * Generate body for method init
     *
     * @return string
     */
    protected function _getBodyForInit()
    {
        $body = '$this->setMethod(\'post\')
     ->setName(\''.$this->_formName.'\')
     ->setDescription(\'Description\');' . "\n";

        foreach ($this->_config as $field) {
            if (!isset($field->PRIMARY)) {
                continue;
            }

            $body .= "\n" . $this->_getField($field);
        }

        $body .= "\n" . $this->_getSubmitButton('Create');

        return $body;
    }

    /**
     * Generate form element submit button
     * @param string $label
     * @return string
     */
    protected function _getSubmitButton($label = 'Create')
    {
        $body[] = '$element = new Zend_Form_Element_Submit(\'submit\');';
        $body[] = '$element->setLabel(\''.$label.'\');';
        $body[] = '$element->setOrder(' . self::$_elementOrder . ');';
        $body[] = '$this->addElement($element);';

        return implode("\n", $body);
    }

    /**
     * Generate fiel element
     *
     * @param Zend_Config $field
     * @return string
     */
    protected function _getField($field)
    {
        $labelField = ucfirst($field->COLUMN_NAME);
        $body = array();

        $elementType = $field->DATA_TYPE;
        if ($field->PRIMARY === true) {
            $elementType = 'hidden';
        }

        $body[] = '$element = new ' . $this->_getElementType($elementType) .
                '(\'' . $field->COLUMN_NAME . '\');';

        if ($elementType != 'hidden') {
            if (($options = $this->_getElementOptions($field->DATA_TYPE))) {
                $body[] = '$element->setOptions('.$options.');';
            }
            if (false === $field->NULLABLE && null == $field->DEFAULT) {
                $body[] = '$element->setRequired(true);';
                $labelField .= ' *';
            }

            $body[] = '$element->setLabel(\'' . $labelField . '\');';
            $body[] = $this->_getValidator($field);
        }
        $body[] = '$element->setOrder(' . self::$_elementOrder++ . ');';
        $body[] = '$this->addElement($element);' . "\n";

        return implode("\n", $body);
    }

    protected function _getElementOptions($fieldType)
    {
        switch($fieldType) {
            case 'date':
                $options = 'array(\'jQueryParams\' => array(\'dateFormat\' => \'yy-mm-dd\'))';
                break;
            default:
                $options = false;
                break;
        }

        return $options;
    }

    /**
     * Get form element type
     *
     * @param string $columnType
     * @return string
     */
    protected function _getElementType($columnType)
    {
        switch ($columnType) {
            case 'date':
                $elementType = 'ZendX_JQuery_Form_Element_DatePicker';
                break;
            case 'hidden':
                $elementType = 'Zend_Form_Element_Hidden';
                break;
            case 'enum':
            default:
                $elementType = 'Zend_Form_Element_Text';
                break;
        }

        return $elementType;
    }

    /**
     * Set element validator
     *
     * @param Zend Config $field
     * @return string
     */
    protected function _getValidator($field)
    {
        switch ($field->DATA_TYPE) {
            case 'date':
                $body = '$element->addValidator(\'Date\', false, array(\'Y-m-d\'));';
                break;
            case 'datetime':
                $body = '$element->addValidator(\'Date\', false, array(\'Y-m-d H:i:s\'));';
                break;
            case 'time':
                $body = '$element->addValidator(\'Date\', false, array(\'H:i:s\'));';
                break;
            case 'int':
            case 'tinyint':
            case 'bigint':
                $body = '$element->addValidator(\'Digits\', false);';
                break;
            default:
                $body = '$element->addValidator(\'StringLength\', false, array(1,'.$field->LENGTH.'));';
                break;
        }

        return $body;
    }
}
