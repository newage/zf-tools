<?php

/**
 * Migration manager tool
 *
 * @category Fixture
 * @package Fixture_Tool_Project_Provider
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Tool_Project_Provider_Fixture
    extends ZFTool_Tool_Project_Provider_Abstract
        implements Zend_Tool_Framework_Provider_Pretendable
{

    protected $_title = '[Records]';

    /**
     * Load data from yml fixture or sql fixture
     *
     * @author V.Leontiev
     * @param type $fixtureName
     */
    public function load($fixtureName = 'all')
    {
        if ($fixtureName == 'all') {
            $fixtureName = null;
        }

        $manager = new ZFTool_Migration_Fixture();
        $manager->load($fixtureName);

        foreach($manager->getMessages() as $message) {
            $this->_print($message['message'], array('color' => $message['color']));
        }
    }

    /**
     * Save database data to yml file
     *
     * @author V.Leontiev
     * @param string $tableName
     * @param string $fixtureName
     */
    public function save($tableName)
    {
        $manager = new ZFTool_Migration_Fixture();
        $manager->save($tableName);

        foreach($manager->getMessages() as $message) {
            $this->_print($message['message'], array('color' => $message['color']));
        }
    }
}
