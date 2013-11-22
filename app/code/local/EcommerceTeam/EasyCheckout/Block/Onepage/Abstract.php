<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Block_Onepage_Abstract
    extends Mage_Checkout_Block_Onepage_Abstract
{
    /** @var EcommerceTeam_EasyCheckout_Helper_Easy */
    protected $_helper;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('ecommerceteam_echeckout/easy');
    }

    /**
     * @return EcommerceTeam_EasyCheckout_Helper_Easy
     */
    public function _getHelper()
    {
        return $this->_helper;
    }

    /**
     * @param $node
     * @return string
     */
    public function getConfigData($node)
    {
        return $this->_helper->getConfigData($node);
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function getCountryHtmlSelect($type)
    {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('general/country/default');
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle($this->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());

        return $select->getHtml();
    }

    /**
     * @return bool
     */
    public function isThreeColsMode()
    {
        return $this->getLayoutMode() == EcommerceTeam_EasyCheckout_Model_System_Conf_Source_Mode::MODE_THREE_COLUMNS;
    }

    public function getLayoutMode()
    {
        return $this->_helper->getConfigData('options/checkoutmode');
    }

    /**
     * @param string $type
     * @return string
     */
    public function getAddressesHtmlSelect($type, $currentCuntryId = null)
    {
        $countryList = array();
        $countries = $this->getCountryOptions();
        foreach ($countries as $country) {
            if ($country['value'] != '')
                $countryList[$country['value']] = '';
        }
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                
                if ($type == "shipping" && !isset($countryList[$address->getCountryId()])) {
                    continue;
                } 
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format('oneline')
                );
                
                if ($type == "shipping" && !is_null($currentCuntryId) && $currentCuntryId == $address->getCountryId()) {
                    $addressId = $address->getId();
                }
            }
            
            if ($type == "shipping" && sizeof($options) == 0) {
                return '';
            }
            //Propongo sempre il primary per semplicità....
            //$addressId = $this->getAddress()->getCustomerAddressId();

            if (empty($addressId)) {
                if ('shipping' == $type) {
                    //l'ho impostato sopra.....
                    if (!isset($addressId)) {
                        $addressId = $options[0]['value'];
                    }
                } else {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            /** @var $select Mage_Core_Block_Html_Select */
            $select = $this->getLayout()->createBlock('core/html_select');
            $select->setName($type.'_address_id');
            $select->setId($type.'-address-select');
            $select->setClass('address-select');
            $select->setExtraParams('onchange="checkout.new' . ucfirst($type) . 'Address(!this.value)"');
            $select->setValue($addressId);
            $select->setOptions($options);
            $select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }

    /**
     * @return bool|int
     */
    public function customerHasAddresses()
    {
        if ($this->_helper->getConfigFlag('options/address_book_enabled')) {
            return parent::customerHasAddresses();
        }
        return false;
    }
}
