<?php
/**
 * Delivery Date and Customer Comment - Magento Extension
 *
 * @package     Ddc
 * @category    EcommerceTeam
 * @copyright   Copyright 2011 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.0
 */

class Ecommerceteam_Ddc_Model_Adminhtml_System_Config_Source_Enablecomment
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('ecommerceteam_ddc');
        return array(
            array('value' => 0, 'label'=>$helper->__('No')),
            array('value' => 1, 'label'=>$helper->__('Yes, Shipping Method Section')),
            array('value' => 2, 'label'=>$helper->__('Yes, Review Section')),
        );
    }
}
