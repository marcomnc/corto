<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author marcoma
 */
class Autel_Corto_Model_Mysql4_Zonegroup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{    

    protected function _construct()
    {
            $this->_init('autelcorto/zonegroup');
    }
    
    public function Sort($order = "ASC") {
        $this->getSelect()->order('sort_order',$order);
        return $this;
    } 
    
    public function toOptionArray() {
        
        $retArr[] = array('value' => '', 'label' => Mage::Helper('autelcorto')->__('Seleziona un gruppo'));
        
        foreach ($this as $group) {
            $retArr[] = array('value' => $group->getId(), 'label' => $group->getGroupName());
        }
        return $retArr;
    }
    
    
}

?>
