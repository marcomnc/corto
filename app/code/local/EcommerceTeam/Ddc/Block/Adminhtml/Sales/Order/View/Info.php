<?php
/**
 * Delivery Date and Customer Comment - Magento Extension
 *
 * @package     Ddc
 * @category    EcommerceTeam
 * @copyright   Copyright 2011 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.0
 */

class EcommerceTeam_Ddc_Block_Adminhtml_Sales_Order_View_Info
    extends Mage_Adminhtml_Block_Sales_Order_View_Info
{

     /**
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        /** @var $helper EcommerceTeam_Ddc_Helper_Data */
        $helper =  Mage::helper('ecommerceteam_ddc');
        if ($helper->getConfigData('enabled_comment')) {
            /** @var $block Mage_Core_Block_Abstract */
            $block = $this->getChild('order_info.ecommerceteam');
        }
    	
        if (isset($block) && $block instanceof Mage_Core_Block_Abstract) {
            $html .= $block->toHtml();
        }

        return parent::_afterToHtml($html);
    }
}