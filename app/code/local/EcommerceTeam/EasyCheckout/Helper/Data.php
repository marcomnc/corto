<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package     EasyCheckout
 * @category    EcommerceTeam
 * @copyright   Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Helper_Data
    extends EcommerceTeam_Core_Helper_Data
{
    protected $mode;
    protected $_checkout;
    protected $_geoIpRecord;

    /**
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        if (empty($this->_checkout)) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    /**
     * @param string $xmlNode
     * @param Mage_Core_Model_Store|null $store
     * @return string
     */
    public function getConfigData($xmlNode, $store = null)
    {
        return parent::_getConfigData($xmlNode, 'checkout', $store);
    }

    /**
     * @param string $xmlNode
     * @param Mage_Core_Model_Store|null $store
     * @return boolean
     */
    public function getConfigFlag($xmlNode, $store = null)
    {
        return parent::_getConfigFlag($xmlNode, 'checkout', $store);
    }

    /**
     * @return int
     */
    public function getDefaultCountryId()
    {
        return intval(Mage::getStoreConfig('general/country/default'));
    }
    /**
     * @return mixed
     */
    public function differentShippingEnabled()
    {
        return $this->getConfigFlag('options/different_shipping_enabled');
    }

    /**
     * @return mixed
     */
    public function couponEnabled()
    {
        return $this->getConfigFlag('options/coupon_enabled');
    }

    /**
     * @return bool
     */
    public function showSubscribe()
    {
        $result = $this->getConfigFlag('options/subscibe_enabled');
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');
        if ($result && $session->isLoggedIn()) {
            /** @var $subscribeModel Mage_Newsletter_Model_Subscriber */
            $subscribeModel = Mage::getModel('newsletter/subscriber');
            $subscribeModel->loadByCustomer($session->getCustomer());
            return !$subscribeModel->getStatus();
        }
        return $result;
    }

    public function getDefaultPaymentMethod()
    {
        $onepage = Mage::getSingleton('checkout/type_onepage');
        $quote   = $onepage->getQuote();
        $store   = $quote ? $quote->getStoreId() : null;
        $methods = Mage::helper('payment')->getStoreMethods($store, $quote);
        return array_shift($methods);
    }

    public function getGeoipRecord()
    {
        if (is_null($this->_geoIpRecord)) {
            if (
                extension_loaded('mbstring') &&
                (
                    $this->getConfigData('options/geoip_country')
                    || $this->getConfigData('options/geoip_state')
                    || $this->getConfigData('options/geoip_post')
                    || $this->getConfigData('options/geoip_city')
                )
            ) {
                $datafile = Mage::getBaseDir('media').'/ecommerceteam/geoip/'.$this->getConfigData('options/geoip_file');

                if(!is_readable($datafile) || !is_file($datafile)){
                    $datafile = Mage::getBaseDir('media').'/ecommerceteam/geoip/default/GeoLiteCity.dat';
                }

                if (is_file($datafile) && is_readable($datafile)) {
                    try {
                        $this->_geoIpRecord = EcommerceTeam_EasyCheckout_Model_GeoIP_Core::getInstance($datafile, EcommerceTeam_EasyCheckout_Model_GeoIP_Core::GEOIP_STANDARD)
                        ->geoip_record_by_addr($_SERVER['REMOTE_ADDR']);
                    } catch (Exception $e) {
                        $this->_geoIpRecord = false;
                    }
                } else {
                    $this->_geoIpRecord = false;
                }
            } else {
                $this->_geoIpRecord = false;
            }
        }
        return $this->_geoIpRecord;
    }
}
