<?php

/**
 * Migration manager tool
 *
 * @category Migrations
 * @package Migrations_Tool_Project_Provider
 * @license New BSD
 * @author V.Leontiev <vadim.leontiev@gmail.com>
 * @link https://bitbucket.org/newage/zf-tool
 */
class ZFTool_Tool_Project_Provider_Migration
    extends ZFTool_Tool_Project_Provider_Abstract
        implements Zend_Tool_Framework_Provider_Pretendable
{

    protected $_title = '[Migration]';

    /**
     * @var Core_Migration_Manager
     */
    protected $_manager = null;

    /**
     * Initialize Core_Migration_Manager
     * Load profile and load development config
     *
     * @author V.Leontiev
     */
    public function initialize()
    {
        $this->_manager = new Core_Migration_Manager();
    }

    /**
     * Create new migration file
     *
     * @author V.Leontiev
     */
    public function create()
    {
        $file = $this->_manager->create();

        $this->_print('Create new migration', array('color' => 'green'));
        $this->_print('Migration file migrations/' . $file . '.php');
    }

    /**
     * Upgrade to migration
     *
     * @author V.Leontiev
     * @param int $migrationNumber
     */
    public function upgrade($toMigration = 'last')
    {
        $this->_manager->migration($toMigration);

        foreach($this->_manager->getMessages() as $message) {
            $this->_print($message['message'], array('color' => $message['color']));
        }
    }

    /**
     * Get current migration
     *
     * @author V.Leontiev
     */
    public function current()
    {
        $migration = $this->_manager->getLastMigration();
        $this->_print('Cuttent migration: '.$migration, array('color' => 'green'));
    }
}

