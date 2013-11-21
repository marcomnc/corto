<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Model_System_Conf_Source_Mode
{
    const MODE_ONE_COLUMN       = 0;
    const MODE_THREE_COLUMNS    = 1;
    const MODE_THREE_COLUMNS_V2 = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $helper EcommerceTeam_EasyCheckout_Helper_Data */
        $helper = Mage::helper('ecommerceteam_echeckout');
        return array(
            array('label' => $helper->__('1 Column'), 'value' => self::MODE_ONE_COLUMN),
            array('label' => $helper->__('3 Columns'), 'value' => self::MODE_THREE_COLUMNS),
            //array('label' => $helper->__('3 Columns (version 2)'), 'value' => self::MODE_THREE_COLUMNS_V2),
        );
    }
}
