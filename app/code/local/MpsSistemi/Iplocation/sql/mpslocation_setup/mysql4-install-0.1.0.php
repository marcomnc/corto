<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('mps_location_zone')} (
  `entity_id` int(6) NOT NULL AUTO_INCREMENT,
  `website_id` int(6) NOT NULL,
  `zone_code` varchar(10) NOT NULL COMMENT 'Codice Della zona',
  `description` varchar(255) NOT NULL COMMENT 'Descrizione della zona',
  `sort_order` int(4) NOT NULL COMMENT 'Ordinamento',
  `state_list` text COMMENT 'Lista dei paesi appartenenti alla zona',
  
  PRIMARY KEY (`entity_id`),
  KEY `zone_code` (`zone_code`)
);

CREATE TABLE IF NOT EXISTS {$this->getTable('mps_location_zone_label')} (
  `entity_id` int(6) NOT NULL AUTO_INCREMENT,
  `zone_id` int(6) not null,
  `store_id` smallint(5),
  `label` varchar (255),
  PRIMARY KEY (`entity_id`),
  KEY IDX_LABEL_STORE (zone_id, store_id)
);


ALTER TABLE {$this->getTable('mps_location_zone_label')}
  ADD CONSTRAINT `FK_ZONE_ID` FOREIGN KEY (`zone_id`) REFERENCES {$this->getTable('mps_location_zone')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
;
  
");
  
$table_group = $installer->getConnection()
    ->newTable($installer->getTable('mpslocation/zonegroup'))
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


$this->getConnection()->addColumn($this->getTable('mpslocation/zone'), 'group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT);
$this->getConnection()->addForeignKey($installer->getFkName('mpslocation/zone', 'group_id', 'mpslocation/zonegroup', 'entity_id'),
        $this->getTable('mpslocation/zone'), 'group_id', $this->getTable('mpslocation/zonegroup'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_RESTRICT, Varien_Db_Ddl_Table::ACTION_RESTRICT);

$groupBase = Mage::getModel('mpslocation/zonegroup');
$groupBase->setGroupName('Base');
$groupBase->setSortOrder(0);
$groupBase->save();

$this->getConnection()->addColumn($this->getTable('mpslocation/zone'), 'store_id', 
        array("COLUMN_TYPE" =>  Varien_Db_Ddl_Table::TYPE_TEXT, 
              "LENGTH" => 50, 
              "COMMENT" => 'Lista store associati alla zona (per identificare lingue disponibili)'));

$installer->endSetup();

?>
