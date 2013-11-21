<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Model_System_Conf_Source_Cmsblock
{
    protected $_options;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            /** @var $blockCollection Mage_Cms_Model_Resource_Block_Collection */
            $blockCollection = Mage::getResourceModel('cms/block_collection');
            $blockCollection->load();

            $this->_options = $blockCollection->toOptionArray();
            array_unshift($this->_options, array('value'=>'', 'label'=>Mage::helper('ecommerceteam_echeckout')->__('Please select a static block ...')));
        }
        return $this->_options;
    }
}
