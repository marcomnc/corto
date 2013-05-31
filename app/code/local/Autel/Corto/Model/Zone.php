<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zone
 *
 * @author marcoma
 */
class  Autel_Corto_Model_Zone extends Mage_Core_Model_Abstract
{
    
    protected function _construct()
    {        
        $this->_init('autelcorto/zone');
    }
    
    public function getId() {
        return $this->getEntityId();
    }
    
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }
        return $this->_getData('store_labels');
    }
    
    public function getStoreLabel($returnDescription=false) {
        $storeId = Mage::app()->getStore()->getId();
        if ($this->hasStoreLabels()) {
            $labels = $this->_getData('store_labels');
            if (isset($labels[$storeId])) {
                return $labels[$storeId];
            } elseif ($labels[0]) {
                return $labels[0];
            } elseif ($returnDescription) {
                return $this->getData('description');
            } else {
                return false;
            }
        } elseif (!isset($this->_labels[$storeId])) {
            $this->_labels[$storeId] = $this->_getResource()->getStoreLabel($this->getId(), $storeId);
        }
        return $this->_labels[$storeId];        
    }
   
    protected function _beforeSave() {

        $zoneList = "";
        if (is_array($this->getStateList())) {
            foreach ($this->getStateList() as $state) {
                if ($zoneList == "") {
                    $zoneList = $state;
                } else {
                    $zoneList .= "," . $state;
                }
            }
            $this->setStateList($zoneList);
        } 
        
        $ws = $this->getWebsiteId();
        $this->setWebsiteId($ws[0]);
        
        parent::_beforeSave();
    }
}

?>
