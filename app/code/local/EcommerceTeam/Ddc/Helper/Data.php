<?php
/**
 * Delivery Date and Customer Comment - Magento Extension
 *
 * @package     Ddc
 * @category    EcommerceTeam
 * @copyright   Copyright 2011 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.0
 */

class EcommerceTeam_Ddc_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigData($node)
    {
        return Mage::getStoreConfig(sprintf('checkout/deliverydatecomment/%s', $node));
    }
}
