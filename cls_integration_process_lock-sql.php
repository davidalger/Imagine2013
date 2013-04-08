<?php

// 
//  install script to setup cls_integration_process_lock table
//  

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'core/resource'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('cls_integration/process_lock'))
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        'primary'   => true,
    ), 'Job Code')
    ->addColumn('locked_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Locked At')
    ->addColumn('freed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Freed At')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, 1, array(
        'nullable'  => false,
        'default'   => 0,
    ), 'Lock Status')
    ->setComment('Holds an atomically set lock to prevent overlapping jobs.');

$installer->getConnection()->createTable($table);
