<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Quote
 *
 * @author doctor
 */
class EcommerceTeam_EasyCheckout_Model_Sales_Quote extends Mage_Sales_Model_Quote {
    
    /**
     * Override della funzione per fare in modo che il carrello venga portato tra uno store e l'altro
     * Ritorna tutti gli store disponibili in modo che la select sul carrello vada a buon fine
     * vedi anche EcommerceTeam_EasyCheckout_Model_Session
     */
    public function getSharedStoreIds() {
        $ids = $this->_getData('shared_store_ids');
        if (is_null($ids) || !is_array($ids)) {
            $ids = Mage::getModel('core/store')->getCollection()->getAllIds();
            unset($ids[0]);//remove admin just in case
            return $ids;
        }
        return $ids;
    }
    
    /**
     * Override della funzione base per ricalcolare il carello nel caso lo store della quota non corrisponda a quello della sessione
     */
    protected function _afterLoad() {
        $recollect = false;
        if ($this->getStoreId() != Mage::app()->getStore()->getId()) {
            $this->setStore(Mage::app()->getStore());
            $this->setData('trigger_recollect',1);
            $recollect = true;
        }
        parent::_afterLoad();
        
        if ($recollect) {
            Mage::dispatchEvent('checkout_cart_save_after', array('force' => true));
        }
    }
}
