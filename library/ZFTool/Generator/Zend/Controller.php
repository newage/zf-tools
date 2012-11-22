<?php

/**
 * Generate Controller code and CRUD actions
 *
 * @category ZFScaffold
 * @package ZFScaffold_Generator
 * @subpackage Zend
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Generator_Zend_Controller
    extends ZFTool_Generator_Abstract
        implements ZFTool_Generator_Interface
{

    private $_modelName;
    private $_formName;
    private $_dbTableName;

    /**
     * @TODO add change created resource, use hasResource() method
     */
    public function generate()
    {
        $moduleName          = ucfirst($this->_config->moduleName);
        $controllerName      = ucfirst($this->_config->tableName);
        $controllerDirectory = $this->_getModulePath() . DIRECTORY_SEPARATOR . 'controllers';
        $this->_modelName    = $moduleName . '_Model_' . $controllerName;
        $this->_formName     = $moduleName . '_Form_' . $controllerName;
        $this->_dbTableName  = $moduleName . '_Model_DbTable_' . $controllerName;

        $controller = new Zend_CodeGenerator_Php_Class();
        $controller->setName(ucfirst($moduleName) . '_' . $controllerName . 'Controller');
        $controller->setDocblock($this->_generateDocBlock());
        $controller->setExtendedClass('Zend_Controller_Action');

        $controller->setMethod($this->_createMethod('createAction'));
        $controller->setMethod($this->_createMethod('deleteAction'));
        $controller->setMethod($this->_createMethod('updateAction'));
        $controller->setMethod($this->_createMethod('readAction'));

        $this->_generateFile(
            $controller,
            $controllerDirectory . DIRECTORY_SEPARATOR . $controllerName . 'Controller.php'
        );
    }

    protected function _getBodyForReadAction()
    {
        $body = '$mapper = new '.$this->_modelName.'Mapper();
$paginator = new Zend_Paginator(
    new Zend_Paginator_Adapter_DbSelect($mapper->getDbTable()->select())
);
$paginator->setCurrentPageNumber($this->_request->getParam(\'page\'));

$this->view->paginator = $paginator;';

        return $body;
    }

    protected function _getBodyForUpdateAction()
    {
        $redirectUrl = '/' . $this->_config->moduleName . '/' . $this->_config->tableName . '/read';

        $body = '
$this->view->headTitle(\'update\');
$validator = new Zend_Validate_Int();
$param = $this->_request->getParam(\'id\');
$form = new '.$this->_formName.'();

if ($param && $validator->isValid($param)) {
    $mapper = new '.$this->_modelName.'Mapper();

    if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
        $model = new ' . $this->_modelName . '($form->getValues());

        if ($mapper->update($model)) {
            $this->_helper->FlashMessenger(\'Update Success\');
            $this->getHelper(\'Redirector\')->gotoUrl(\''.$redirectUrl.'\');
        } else {
            $form->addErrors(array(\'Don\\\'t update\'));
            $form->addDecorator(\'Errors\', array(\'placement\'=>\'PREPEND\'));
        }
    } else {
        $form->update($mapper->getDbTable()->find($param)->getRow(0));
    }
} else {
    $this->_helper->FlashMessenger(\'Error validate\');
}
$this->view->form = $form;';

        return $body;
    }

    protected function _getBodyForCreateAction()
    {
        $redirectUrl = '/' . $this->_config->moduleName . '/' . $this->_config->tableName . '/read';

        $body = '
$this->view->headTitle(\'index\');
$form = new '.$this->_formName.'();

if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
    $mapper = new '.$this->_modelName.'Mapper();
    $model = new ' . $this->_modelName . '($form->getValues());

    if ($mapper->create($model)) {
        $this->_helper->FlashMessenger(\'Success\');
        $this->getHelper(\'Redirector\')->gotoUrl(\''.$redirectUrl.'\');
    } else {
        $form->addErrors(array(\'Error\'));
        $form->addDecorator(\'Errors\', array(\'placement\'=>\'PREPEND\'));
    }
}
$this->view->form = $form;';

        return $body;
    }

    protected function _getBodyForDeleteAction()
    {
        $redirectUrl = '/' . $this->_config->moduleName . '/' . $this->_config->tableName . '/read';
        $body = '
$validator = new Zend_Validate_Int();
$param = $this->_request->getParam(\'id\');

if ($param && $validator->isValid($param)) {
    $mapper = new '.$this->_modelName.'Mapper();
    $model = new ' . $this->_modelName . '($this->_request->getParams());

    if ($mapper->delete($model)) {
        $this->_helper->FlashMessenger(\'Delete Successful\');
    } else {
        $this->_helper->FlashMessenger(\'Delete Unsuccessful\');
    }
} else {
    $this->_helper->FlashMessenger(\'Error validate\');
}
$this->getHelper(\'Redirector\')->gotoUrl(\''.$redirectUrl.'\');';

        return $body;
    }
}
