<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Model_Session
    extends Mage_Checkout_Model_Session
{
    /**
     * Clear old data
     */
    public function clear()
    {
        parent::clear();
        $this->unsetData('customer_loaded');
    }

    /**
     * Override della funzione per fare in modo che il carrello venga portato tra uno store e l'altro
     * Imposta la chiave di memorizzazione della quota nella sessione senza il website
     * vedi anche EcommerceTeam_EasyCheckout_Model_Sales_Quote
     * @return type
     */
    protected function _getQuoteIdKey()
    {
        return 'quote_id'; //_' . Mage::app()->getStore()->getWebsiteId();
    }

    
}
