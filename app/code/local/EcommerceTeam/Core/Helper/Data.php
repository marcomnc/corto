<?php
/**
 * @package:    Core
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2012 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    1.0.0
 */

class EcommerceTeam_Core_Helper_Data
    extends Mage_Core_Helper_Data
{
    /** @var array */
    protected $_configCache = array();

    /**
     * @param string $xmlNode
     * @param string $prefix
     * @param Mage_Core_Model_Store|int|string $store
     * @return bool
     */
    protected function _getConfigData($xmlNode, $prefix, $store = null)
    {
        if (!array_key_exists($xmlNode, $this->_configCache)) {
            $this->_configCache[$xmlNode] = Mage::getStoreConfig($prefix . '/' . $xmlNode, $store);
        }
        return $this->_configCache[$xmlNode];
    }

    /**
     * @param string $xmlNode
     * @param string $prefix
     * @param Mage_Core_Model_Store|int|string $store
     * @return bool
     */
    protected function _getConfigFlag($xmlNode, $prefix, $store = null)
    {
        if (!array_key_exists($xmlNode, $this->_configCache)) {
            $this->_configCache[$xmlNode] = Mage::getStoreConfigFlag($prefix . '/' . $xmlNode, $store);
        }
        return $this->_configCache[$xmlNode];
    }
}
