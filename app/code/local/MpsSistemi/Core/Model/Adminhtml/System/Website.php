<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Website
 *
 * @author marcoma
 */
class MpsSistemi_Core_Model_Adminhtml_System_Website {
    
    public function toOptionArray() {
        $ws = Mage::getModel('core/website')->getCollection();
        $arr = array();
        foreach ($ws as $w) {
            if ($w->getId() != 0) {
                $arr[] = array('value' => $w->getId(), 'label' => $w->getName());
            }
        }
        return $arr;
    }
    
    public function StoreToOptionArray() {
        
        $arr =array();
        foreach (MAge::GetModel('core/store') as $store) {
            if ($store != 0 && $store->getIsActive()) {
                $arr[] = array('value' => $store->getId(), 'label' => $store->getName());
            }
        }
        return $arr;
    }
    
}

?>
