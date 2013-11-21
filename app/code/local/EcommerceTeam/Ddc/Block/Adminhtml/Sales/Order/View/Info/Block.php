<?php
/**
 * Delivery Date and Customer Comment - Magento Extension
 *
 * @package     Ddc
 * @category    EcommerceTeam
 * @copyright   Copyright 2011 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.0
 */

class EcommerceTeam_Ddc_Block_Adminhtml_Sales_Order_View_Info_Block
    extends Mage_Core_Block_Template
{
    /** @var Mage_Sales_Model_Order */
    protected $order;

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->order)) {
            if (Mage::registry('current_order')) {
                $order = Mage::registry('current_order');
            } elseif (Mage::registry('order')) {
                $order = Mage::registry('order');
            } else {
                $order = new Varien_Object();
            }
            $this->order = $order;
        }
        return $this->order;
    }
}
