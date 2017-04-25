<?php

/**
 * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer
 */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
        ->newTable($installer->getTable('mpspam/penalty'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Account Id')
        ->addColumn('ip', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                ), 'IP Address Name')
        ->addColumn('penalty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => 1,
                ), 'Penalty Counter')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                ), 'Created At')
        ->addIndex($installer->getIdxName($installer->getTable('mpspam/penalty'), array('ip'),
                        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
                'ip', array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
        )
        ->setComment('Rakuten Account Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
