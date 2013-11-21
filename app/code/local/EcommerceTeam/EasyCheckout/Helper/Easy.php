<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Helper_Easy
        extends EcommerceTeam_EasyCheckout_Helper_Data
{

    const POSITION_MODE_DEFAULT = 'default';
    const POSITION_MODE_CART    = 'cart';

    /** @var string */
    protected $_mode;

    /**
     * @return bool
     */
    public function isDeveloperMode()
    {
        return false;
    }

    /**
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    public function getCheckoutModel()
    {
        return Mage::getSingleton('ecommerceteam_easycheckout/type_easy');
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return $this->getCheckoutModel()->getSession();
    }

    /**
     * @return string
     */
    public function getSkin()
    {
        return $this->getConfigData('options/skin');
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckoutModel()->getQuote();
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getDefaultPaymentMethod()
    {
        $quote   = $this->getCheckoutModel()->getQuote();
        $store   = $quote ? $quote->getStoreId() : null;
        /** @var $paymentHelper Mage_Payment_Helper_Data */
        $paymentHelper = Mage::helper('payment');
        $methods = $paymentHelper->getStoreMethods($store, $quote);
        return array_shift($methods);
    }

    /**
     * @return string
     */
    public function getDefaultCountryId()
    {
        /** @var $geoIpRecord object */
        $geoIpRecord = $this->getGeoipRecord();
        if (is_object($geoIpRecord) && $geoIpRecord->country_code) {
            return $geoIpRecord->country_code;
        }
        return Mage::getStoreConfig('general/country/default');
    }


    /**
     * Check is allowed Guest Checkout
     * Use config settings and observer
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param null|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isAllowedGuestCheckout(Mage_Sales_Model_Quote $quote = null, $store = null)
    {
        /** @var $helper Mage_Checkout_Helper_Data */
        $helper = Mage::helper('checkout');
        if (is_null($quote)) {
            $quote = $this->getQuote();
        }
        return $helper->isAllowedGuestCheckout($quote, $store);
    }

    public function canShip()
    {
        return !$this->getQuote()->isVirtual();
    }

    /**
     * @return string
     */
    public function getCheckoutMode()
    {
        if (is_null($this->_mode)) {
            $request = Mage::app()->getRequest();
            if ('cart' == $request->getControllerName() && 'index' == $request->getActionName()) {
                $this->_mode = self::POSITION_MODE_CART;
            } else {
                $this->_mode = self::POSITION_MODE_DEFAULT;
            }
        }
        return $this->_mode;
    }

}
