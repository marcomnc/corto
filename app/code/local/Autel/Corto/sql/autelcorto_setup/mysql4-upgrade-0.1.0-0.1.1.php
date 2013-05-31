<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();


$table_faq = $installer->getConnection()
    ->newTable($installer->getTable('autelcorto/faq'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,        
        ), 'Title')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, 6, array(
        'nullable'  => true,        
        ), 'Ordinamento')
    ->addColumn('faq_serial', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,        
        ), 'Lista delle faq sotto forma di array serializzato');
$installer->getConnection()->createTable($table_faq);

$table_faq_store = $installer->getConnection()
    ->newTable($installer->getTable('autelcorto/faq_store'))
    ->addColumn('faq_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Faq ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addIndex($installer->getIdxName('autelcorto/faq_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('autelcorto/faq_store', 'faq_id', 'autelcorto/faq', 'entity_id'),
        'faq_id', $installer->getTable('autelcorto/faq'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('autelcorto/faq_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table_faq_store);

$installer->endSetup();

?>
