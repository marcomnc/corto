<?php
/**
 * Delivery Date and Customer Comment - Magento Extension
 *
 * @package     Ddc
 * @category    EcommerceTeam
 * @copyright   Copyright 2011 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.0
 */

class EcommerceTeam_Ddc_Model_Resource_Mysql4_Order extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('ecommerceteam_ddc/order', 'entity_id');
    }
}
