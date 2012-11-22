<?php

require_once __DIR__ . '/library/ZFTool/Tool/Project/Provider/Scaffold.php';

class Manifest
    implements Zend_Tool_Framework_Manifest_Interface,
        Zend_Tool_Framework_Manifest_ProviderManifestable
{

    public function getProviders()
    {
        return array(
            new ZFTool_Tool_Project_Provider_Scaffold()
        );
    }
}

