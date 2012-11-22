<?php

/**
 * Scafold generator abstract
 *
 * @category ZFScaffold
 * @package ZFScaffold_Generator
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
abstract class ZFTool_Generator_Abstract
{

    protected $_stringSeparator = "\n";

    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @var Zend_Tool_Project_Profile
     */
    protected $_profile;

    /**
     * Initialize start variable
     *
     * @param Zend_Config $config
     * @param Zend_Tool_Project_Profile $profile
     * @return void
     */
    public function init(Zend_Config $config, Zend_Tool_Project_Profile $profile)
    {
        $this->_config = $config;
        $this->_profile = $profile;
    }

    /**
     * Create method use Zend_CodeGenerator_Php_Method
     *
     * @param string $methodName
     * @param array  $params
     * @param string $body
     * @param string $visibility
     * @return Zend_CodeGenerator_Php_Method
     */
    protected function _createMethod($methodName, $params = array(), $body = null, $visibility = 'public')
    {
        $thisMethodName = '_getBodyFor' . ucfirst($methodName);
        if (method_exists($this, $thisMethodName)) {
            $body = $this->$thisMethodName();
        }

        $method = new Zend_CodeGenerator_Php_Method();
        $method->setName($methodName);
        $method->setVisibility($visibility);
        $method->setBody($body);

        if (is_array($params) && !empty($params)) {
            $method->setParameters($params);
        }

        return $method;
    }

    /**
     * Generate standart docblock
     *
     * @return Zend_CodeGenerator_Php_Docblock
     */
    protected function _generateDocBlock()
    {
        return new Zend_CodeGenerator_Php_Docblock(
            array(
                'longDescription'  => 'This is a class generated with Zend_CodeGenerator.',
                'tags'             => array(
                    array(
                        'name'        => 'category',
                        'description' => '###CATEGORY###'
                    ),
                    array(
                        'name'        => 'package',
                        'description' => '###PACKAGE###'
                    ),
                    array(
                        'name'        => 'subpackage',
                        'description' => '###SUBPACKAGE###'
                    ),
                    array(
                        'name'        => 'version',
                        'description' => '$Id$',
                    ),
                    array(
                        'name'        => 'license',
                        'description' => 'New BSD',
                    ),
                )
            )
        );
    }

    /**
     * Get path to resource
     *
     * @param string $resourceType
     * @return string
     */
    protected function _getModulePath()
    {
        $matchSearchConstraints = array(
            'modulesDirectory',
            'moduleDirectory' => array(
                'moduleName' => $this->_config->moduleName
             )
         );

        $result = $this->_profile->search($matchSearchConstraints);
        return $result->getPath();
    }

    /**
     * Write body to file
     *
     * @param string $body
     * @param string $filePath
     */
    protected function _generateFile($body, $filePath)
    {
        $this->_createDir(dirname($filePath));

        if (!file_exists($filePath) || $this->_config->rewrite) {
            $fileModel = new Zend_CodeGenerator_Php_File();
            $fileModel->setBody($body);
            $fileModel->setFilename($filePath);

            $fileModel->write();
        }
    }

    /**
     * Create not exists directories
     *
     * @param string $directory directroy path
     * @return void
     */
    protected function _createDir($directory)
    {
        if (!is_dir($directory)) {
            $previewDir = substr($directory, 0, -strlen(strrchr($directory, DIRECTORY_SEPARATOR)));
            $this->_createDir($previewDir);
            $newDir = substr($directory, -strlen(strrchr($directory, DIRECTORY_SEPARATOR))+1);

            chdir($previewDir);
            mkdir($newDir);
        }
        return;
    }
}
