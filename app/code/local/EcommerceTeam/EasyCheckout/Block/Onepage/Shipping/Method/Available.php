<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Block_Onepage_Shipping_Method_Available
    extends Mage_Checkout_Block_Onepage_Abstract
{
    /** @var array */
    protected $_rates;
    /** @var Mage_Sales_Model_Quote_Address */
    protected $_address;

    /**
     * @return array
     */
    public function getShippingRates()
    {
        if (is_null($this->_rates)) {
            $this->getAddress()->collectShippingRates();
            return $this->_rates = $this->getAddress()->getGroupedAllShippingRates();
        }
        return $this->_rates;
    }

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    /**
     * @param string $carrierCode
     * @return string
     */
    public function getCarrierName($carrierCode)
    {
        $name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');
        return $name ? $name : $carrierCode;
    }

    /**
     * @return string
     */
    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    /**
     * @param float $price
     * @param bool $flag
     * @return float
     */
    public function getShippingPrice($price, $flag)
    {
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper     = Mage::helper('tax');
        /** @var $shippingPrice double */
        $shippingPrice = $taxHelper->getShippingPrice($price, $flag, $this->getAddress());
        return $this->getQuote()->getStore()->convertPrice($shippingPrice, true);
    }
}
