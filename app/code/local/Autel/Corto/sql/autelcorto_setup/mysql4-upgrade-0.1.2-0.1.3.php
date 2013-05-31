<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();


$table_group = $installer->getConnection()
    ->newTable($installer->getTable('autelcorto/zonegroup'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('group_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,        
        ), 'Nome del gruppo')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, 6, array(
        'nullable'  => true,        
        ), 'Ordinamento');
$installer->getConnection()->createTable($table_group);


$this->getConnection()->addColumn($this->getTable('autelcorto/zone'), 'group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT);
$this->getConnection()->addForeignKey($installer->getFkName('autelcorto/zone', 'group_id', 'autelcorto/zonegroup', 'entity_id'),
        $this->getTable('autelcorto/zone'), 'group_id', $this->getTable('autelcorto/zonegroup'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_RESTRICT, Varien_Db_Ddl_Table::ACTION_RESTRICT);

$groupBase = Mage::getModel('autelcorto/zonegroup');
$groupBase->setGroupName('Base');
$groupBase->setSortOrder(0);
$groupBase->save();

foreach (Mage::getModel('autelcorto/zone')->getCollection() as $z) {
    $zone = Mage::getModel('autelcorto/zone')->Load($z->getId());
    $zone->setGroupId($groupBase->getId());
    $zone->save();
}


$installer->endSetup();

?>
