<?php

/**
 * Nead methods in all providers
 *
 * @category Library
 * @package Library_Tool_Project_Provider
 * @author Vadim Leontiev <vadim.leontiev@gmail.com>
 * @see https://bitbucket.org/newage/zf-tool
 * @since php 5.1 or higher
 */
abstract class ZFTool_Tool_Project_Provider_Abstract extends Zend_Tool_Project_Provider_Abstract
{

    /**
     * Title for console messages
     * @var string
     */
    protected $_title = '';

    /**
     * @var Zend_Application
     */
    protected $_app = null;

    protected static $_bootstrap = false;

    /**
     * Initialize ZFTool_Migration_Manager
     * Load profile and load development config
     *
     * @author V.Leontiev
     */
    public function initialize()
    {
//        if (!self::$_isInitialized) {
            parent::initialize();
            $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);
            $this->_bootstrapWithOtherConfig();
//        }
    }

    /**
     * Get Application with load development config
     *
     * @author V.Leontiev
     */
    protected function _bootstrapWithOtherConfig()
    {
        if (!self::$_bootstrap) {
            $profile = $this->_loadProfileRequired();
            $this->_app = $profile->search('BootstrapFile')->getApplicationInstance();

            $this->_app->setOptions(array_merge(
                $this->_app->getOptions(),
                $this->_getDevelopmentConfig()
            ));
            $this->_app->bootstrap();

            self::$_bootstrap = true;
        } else {
            $applicationEnv = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production';
            $this->_app = new Zend_Application(
                    $applicationEnv,
                    $this->_getDevelopmentConfig()
            );
        }
    }

    /**
     * Get config
     *
     * @author V.Leontiev
     * @return array
     */
    private function _getDevelopmentConfig()
    {
        $configPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs';
        if (!defined('APPLICATION_ENV')) {
            define('APPLICATION_ENV', 'development');
        }
        if (file_exists($configPath . '/application.' . APPLICATION_ENV . '.ini')) {
            $config = new Zend_Config_Ini($configPath . '/application.' . APPLICATION_ENV . '.ini', APPLICATION_ENV);
            return $config->toArray();
        } else {
            $config = new Zend_Config_Ini($configPath . '/application.ini', APPLICATION_ENV);
            return $config->toArray();
        }
    }

    /**
     * Print message to console
     *
     * @author V.Leontiev
     * @param string $line
     * @param array $decoratorOptions
     */
    protected function _print($line, array $decoratorOptions = array())
    {
        $this->_registry->getResponse()->appendContent($this->_title . ' ' . $line, $decoratorOptions);
    }

    /**
     * Print content message
     *
     * @author V.Leontiev
     * @param string $line
     * @param array $decoratorOptions
     */
    protected function _content($line, array $decoratorOptions = array())
    {
        if (empty($decoratorOptions)) {
            $decoratorOptions = array('color' => 'yelow');
        }
        $this->_registry->getResponse()->appendContent('Note: ' . $line, $decoratorOptions);
    }
}
