<?php

/**
 * Create CRUD files on your project
 *
 * @category ZFScaffold
 * @package ZFScaffold_Generator
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Generator_Generator
{
    /**
     * Start generate all resource
     *
     * @param Zend_Tool_Project_Profile $profile
     * @return array
     */
    public function startGenerate(Zend_Config $config, Zend_Tool_Project_Profile $profile)
    {
        $messages = array();

        $adapterName = ucfirst($config->get('adapterName'));
        $objectName = 'ZFTool_Adapter_' . $adapterName;
        $adapter = new $objectName($config->get('tableName'));

        $config->merge($adapter->getColumnsFromTable());

        $generators = $this->_getFilesFromDir(dirname(__FILE__) . DIRECTORY_SEPARATOR . $adapterName);

        foreach ($generators as $value) {
            $objectName = 'ZFTool_Generator_' . $adapterName . '_' . $value;
            $object = new $objectName();
            $object->init($config, $profile);
            $messages[] = $object->generate();
        }

        return $messages;
    }

    /**
     * Read only files from dir
     *
     * @param string $dir
     * @return array
     */
    protected function _getFilesFromDir($dir)
    {
        $generators = array();
        $directory = dir($dir);
        while (false !== ($entry = $directory->read())) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $info = pathinfo($entry);
            $generators[] = $info['filename'];
        }
        $directory->close();
        return $generators;
    }
}
