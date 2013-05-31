<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('autel_corto_zone')} (
  `entity_id` int(6) NOT NULL AUTO_INCREMENT,
  `website_id` int(6) NOT NULL,
  `zone_code` varchar(10) NOT NULL COMMENT 'Codice Della zona',
  `description` varchar(255) NOT NULL COMMENT 'Descrizione della zona',
  `sort_order` int(4) NOT NULL COMMENT 'Ordinamento',
  `state_list` text COMMENT 'Lista dei paesi appartenenti alla zona',
  
  PRIMARY KEY (`entity_id`),
  KEY `zone_code` (`zone_code`)
);

CREATE TABLE IF NOT EXISTS {$this->getTable('autel_corto_zone_label')} (
  `entity_id` int(6) NOT NULL AUTO_INCREMENT,
  `zone_id` int(6) not null,
  `store_id` smallint(5),
  `label` varchar (255),
  PRIMARY KEY (`entity_id`),
  KEY IDX_LABEL_STORE (zone_id, store_id)
);


ALTER TABLE {$this->getTable('autel_corto_zone_label')}
  ADD CONSTRAINT `FK_ZONE_ID` FOREIGN KEY (`zone_id`) REFERENCES {$this->getTable('autel_corto_zone')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
;
  
");

$installer->endSetup();

?>
