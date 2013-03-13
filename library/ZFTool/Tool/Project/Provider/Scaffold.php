<?php

/**
 * Scaffold generation tool provider
 * Generate module used ZF folder sructure
 * Generate Controller, Model, ModelMapper, DbTable, Form, View
 *
 * @category Library
 * @package Library_Tool_Project_Provider
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
 */
class ZFTool_Tool_Project_Provider_Scaffold
    extends Zend_Tool_Project_Provider_Abstract
        implements Zend_Tool_Framework_Provider_Pretendable
{

    /**
     * @var Zend_Db_Adapter_Interface
     */
    protected $_db = null;

    /**
     * Adapter name
     * @var string
     */
    protected $_adapterName = null;

    /**
     * Registration scaffold resource in config file
     *
     * @param string $dbAdapter
     * @return void
     */
    public function registration($dbAdapter = 'Zend')
    {
        $profile = $this->_loadProfileRequired();

        $applicationConfigResource = $profile->search('ApplicationConfigFile');

        if (!$applicationConfigResource) {
            throw new Zend_Tool_Project_Exception(
                'A project with an application config file is required to use this provider.'
            );
        }

        $config = $applicationConfigResource->getAsZendConfig();

        if (isset($config->resources) && isset($config->resources->scaffold)) {
            $this->_print('A Scaffold resource already exists in this project');
            return;
        }

        $adapterPath = realpath(dirname(__FILE__) . '/../Adapter/' . $dbAdapter . '.php');
        if (!file_exists($adapterPath)) {
            $this->_print('Not exists db adapter', array('color' => 'red'));
            return;
        }

        $applicationConfigResource->addStringItem('autoloaderNamespaces[]', "ZFTool", 'production');
        $applicationConfigResource->create();
        $applicationConfigResource->addStringItem(
            'pluginPaths.ZFTool_Application_Resource',
            'ZFScaffold/Application/Resource',
            'production'
        );
        $applicationConfigResource->create();
        $applicationConfigResource->addStringItem('resources.scaffold.adapter.db_adapter', $dbAdapter, 'production');
        $applicationConfigResource->create();

        $this->_print('Add resource variable to production config file', array('color' => 'green'));
    }

    /**
     * Create module with IndexController, Form, View scripts(CRUD),
     * Model(CRUD operations) and DbTable
     *
     * @param string $realTableName Real table name in bd
     * @param string $moduleName Module name in your project
     * @param bool $force Rewrite exists resources
     * @return void
     */
    public function create($realTableName, $moduleName, $force = false)
    {
        $this->_setProjectConfigs();

        if (!$this->_hasRegisterModule($moduleName)) {
            $this->_print('Need create a module in the project!');
            $this->_print('Execute > zf create module '.$moduleName);
            return;
        }


        $config = new Zend_Config(
            array(
                'adapterName' => $this->_adapterName,
                'tableName' => $realTableName,
                'moduleName' => $moduleName,
                'rewrite' => $force
            ), true
        );

        $generator = new ZFScaffold_Generator_Generator();
        $result = $generator->startGenerate($config, $this->_loadProfileRequired());

        if ($force) {
            $this->_print('Rewrited exists resources');
        }
        if ($result === false) {
            $this->_print('Error created scaffold resources', array('color' => 'red'));
        } else {
            $this->_print('Successful created scaffold resources', array('color' => 'green'));
        }
        return;
    }

    /**
     * Check register module in project resource
     *
     * @param string $moduleName
     * @param bool
     */
    protected function _hasRegisterModule($moduleName)
    {
        $profile = $this->_loadProfileRequired();
        $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => array('moduleName' => $moduleName));

        return ($profile->search($profileSearchParams) instanceof Zend_Tool_Project_Profile_Resource);
    }

    /**
     * Print string
     * @param string $line
     * @param array $decoratorOptions
     */
    protected function _print($line, array $decoratorOptions = array())
    {
        $this->_registry->getResponse()->appendContent('[Scaffold] ' . $line, $decoratorOptions);
    }

    /**
     * Get scafold proviter type
     *
     * @return void
     */
    protected function _setProjectConfigs()
    {
        if (null === $this->_adapterName) {
            $profile = $this->_loadProfileRequired();
            $this->_app = $profile->search('BootstrapFile')->getApplicationInstance();
            $this->_mergeWithDevelopmentConfig();
            $this->_app->bootstrap();

            $container = $this->_app->getBootstrap()->getContainer();

            if (!isset($container->scaffold)) {
                throw ZFScaffold_Exception::dontScaffoldContainer();
            }

            $this->_adapterName = $container->scaffold;
        }
    }

    /**
     * Merge config with development config
     *
     * @return void
     */
    protected function _mergeWithDevelopmentConfig()
    {
        $configPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs';

        if (file_exists($configPath . '/application.development.ini')) {
            $config = new Zend_Config_Ini($configPath . '/application.development.ini', 'development');

            $this->_app->setOptions(array_merge($this->_app->getOptions(), $config->toArray()));
        }
        return;
    }
}

