<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Block_Onepage_Shipping_Method
    extends Mage_Checkout_Block_Onepage_Abstract
{
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }
}
