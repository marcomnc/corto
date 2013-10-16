<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

// Blocchi da visualizzare nella pagina.
// Per ora il controlo di integrita viene fatto a mano

$table_color_attribute = $installer->getConnection()
    ->newTable($installer->getTable('autelcorto/pageblocks'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,        
        ), 'Id Pagina')
    ->addColumn('block_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,        
        ), 'Id Blocco')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,        
        ), 'Ordinamento')
    ->addColumn('fill', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => 0,
        ), 'Se attivo viene impostato come Width/Height as 100%')
    ->addColumn('width', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,        
        ), 'Dimensione larghezza in pixel impostata')
    ->addColumn('height', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,        
        ), 'Dimensione altezza in pixel impostata')
    ->addColumn('style', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true,        
        ), 'Eventuali stili impostati al div esterno')
    ->addColumn('class', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true,        
        ), 'Eventuali calssi impostatate al div esterno')
    ->addIndex(
        $installer->getIdxName(
            'autelcorto/pageblocks',
            array('page_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('page_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX));

$installer->getConnection()->createTable($table_color_attribute);

$installer->endSetup();

?>
