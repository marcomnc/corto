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
class  Autel_Corto_Model_Zonegroup extends Mage_Core_Model_Abstract
{
    
    protected function _construct()
    {        
        $this->_init('autelcorto/zonegroup');
    }
    
    public function getId() {
        return $this->getEntityId();
    }
    
    public function _beforeDelete() {
        
        $zone = Mage::getModel('autelcorto/zone')
                        ->getCollection()
                        ->addAttributeTofilter('group_id', $this->getId());
        foreach ($zone as $z) {
            Mage::throwException(Mage::Helper('autelcorto')->__('Ci sono zone collegate al gruppo'));
        }
        return $this;
    }
    
    public function getName() {
echo "<pre>";
print_r($this->getResource()->getData());
die();
        return $this->getResource()->getName();
    }
    
    public function setName($object) {
        $this->getResource()->setName($object);
    }
}

?>
