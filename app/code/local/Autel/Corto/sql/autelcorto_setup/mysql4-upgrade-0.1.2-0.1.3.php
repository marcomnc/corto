<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$this->getConnection()->addColumn($this->getTable('autelcorto/zone'), 'store_id', 
        array("COLUMN_TYPE" =>  Varien_Db_Ddl_Table::TYPE_TEXT, 
              "LENGTH" => 50, 
              "COMMENT" => 'Lista store associati alla zona (per identificare lingue disponibili)'));

foreach (Mage::getModel('autelcorto/zone')->getCollection() as $z) {
    $zone = Mage::getModel('autelcorto/zone')->Load($z->getId());
    $storeId = '';
    foreach (Mage::getModel('core/store')->getCollection() as $store) {
        if ($store->getWebsiteId() == $zone->getWebsiteId()) {
            $storeId .= ($storeId != '') ? ',' : '';
            $storeId .= $store->getId();
        }
    }
    $zone->setStoreId($storeId);
    $zone->save();
}

$installer->endSetup();

?>
