<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();


$table_debug_import = $installer->getConnection()
    ->newTable($installer->getTable('autelcatalog/import'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,                
        ), 'Codice Session')
    ->addColumn('start_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,        
        ), 'Data Ora Inizio')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,        
        ), 'Tipo importazione')
    ->addColumn('tot_record', Varien_Db_Ddl_Table::TYPE_INTEGER, 8, array(
        'nullable'  => false,        
        ), 'Numero di record da importare')
    ->addColumn('elab_record', Varien_Db_Ddl_Table::TYPE_INTEGER, 8, array(
        'nullable'  => true,        
        ), 'Numero di record imporati')
   ->addColumn('last_update', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,        
        ), 'data ultimo aggiornamento')
   ->addColumn('end_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,        
        ), 'Data Ora fine')
    ->addIndex(
        $installer->getIdxName(
            'autelcatalog/import',
            array('session_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('session_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));

$table_debug_import_message = $installer->getConnection()
    ->newTable($installer->getTable('autelcatalog/message'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,        
        'primary'   => true,
        ), 'Codice Session')
    ->addColumn('event_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,        
        ), 'Data Ora Inizio')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,        
        ), 'Tipo importazione')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,        
        ), 'Messaggio')
    ->addIndex(
        $installer->getIdxName(
            'autelcatalog/message',
            array('session_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('session_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX));
    
$installer->getConnection()->createTable($table_debug_import);

$installer->getConnection()->createTable($table_debug_import_message);




$installer->endSetup();

?>
