<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Block_Onepage_Billing
    extends EcommerceTeam_EasyCheckout_Block_Onepage_Abstract{

    /** @var string */
    protected $_prefix = 'billing';

    /** @var Mage_Directory_Model_Resource_Country_Collection */
    protected $_countryCollection;

    /** @var Mage_Sales_Model_Quote_Address */
    protected $_address;

    /**
     * @return Mage_Directory_Model_Resource_Country_Collection
     */
    public function getCountries()
    {
        if (is_null($this->_countryCollection)) {
            $this->_countryCollection = Mage::getResourceModel('directory/country_collection');;
            $this->_countryCollection->loadByStore();
        }
        return $this->_countryCollection;
    }

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = $this->getQuote()->getBillingAddress();
        }
        return $this->_address;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        $firstName = trim($this->getAddress()->getFirstname());
        if (!$firstName) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->getQuote()->getCustomer();
            if ($customer) {
                $firstName = $customer->getData('firstname');
            }
        }
        return $firstName;
    }

    /**
     * @return mixed|string
     */
    public function getLastname()
    {
        $lastName = trim($this->getAddress()->getLastname());
        if (!$lastName) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->getQuote()->getCustomer();
            if ($customer) {
                $lastName = $customer->getData('lastname');
            }
        }
        return $lastName;
    }
}
