<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();


$table_color_attribute = $installer->getConnection()
    ->newTable($installer->getTable('autelcorto/coloroptions'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,        
        ), 'Id attributo EAV')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,        
        ), 'Id Opzione')
    ->addColumn('img_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true,        
        ), 'Immagine associata')
    ->addColumn('color_hex', Varien_Db_Ddl_Table::TYPE_VARCHAR, 8, array(
        'nullable'  => true,        
        ), 'Codice esadecimale del colore')
    ->addIndex(
        $installer->getIdxName(
            'autelcorto/coloroptions',
            array('option_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('option_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));

$installer->getConnection()->createTable($table_color_attribute);




$installer->endSetup();

?>
