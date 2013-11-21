<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Block_Onepage_Shipping
    extends EcommerceTeam_EasyCheckout_Block_Onepage_Abstract
{
    /** @var string */
    protected $_prefix = 'shipping';

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if ($this->someAsBilling()) {
            /** @var $session Mage_Customer_Model_Session */
            $session = Mage::getSingleton('customer/session');
            if($session->isLoggedIn()){
                if($address = $session->getCustomer()->getDefaultShippingAddress()){
                    return $address;
                }
            }
        }
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }

    /**
     * @return bool
     */
    public function someAsBilling()
    {
        if ($this->_helper->differentShippingEnabled()) {
            if (is_null($this->getQuote()->getShippingAddress()->getData('same_as_billing'))) {
                return true;
            }
            return (bool)($this->getQuote()->getShippingAddress()->getData('same_as_billing'));
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        if (!$this->getQuote()->isVirtual()) {
            if ($this->_helper->differentShippingEnabled()) {
                return true;
            }
        }
        return false;
    }
}
