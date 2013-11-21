<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Model_System_Conf_Source_Skin
{
    const SKIN_DEFAULT      = 'default';
    const SKIN_MOERN_BLUE   = 'modern';
    const SKIN_MOERN_GRAY   = 'modern-gray';
    const SKIN_MOERN_GREEN  = 'modern-green';
    const SKIN_MOERN_ORANGE = 'modern-orange';
    const SKIN_MOERN_BLACK  = 'modern-black';

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
            array('label' => $helper->__('Default'),  'value' => self::SKIN_DEFAULT),
            array('label' => $helper->__('Modern'),   'value' => self::SKIN_MOERN_BLUE),
//            array('label' => $helper->__('Modern (gray)'),   'value' => self::SKIN_MOERN_GRAY),
//            array('label' => $helper->__('Modern (green)'),  'value' => self::SKIN_MOERN_GREEN),
//            array('label' => $helper->__('Modern (orange)'), 'value' => self::SKIN_MOERN_ORANGE),
//            array('label' => $helper->__('Modern (black)'),  'value' => self::SKIN_MOERN_BLACK),
        );
    }
}
