<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Model_System_Conf_Source_Cctypes
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{

    protected $_options;

    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            /** @var $paymentConfig Mage_Payment_Model_Config */
            $paymentConfig = Mage::getSingleton('payment/config');
            $types = $paymentConfig->getCcTypes();
            foreach ($types as $code=>$name) {
                $this->_options[] = array('value'=>$code, 'label'=>$name);
            }
        }
        return $this->_options;
    }
}
