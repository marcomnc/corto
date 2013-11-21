<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Block_Checkout
        extends EcommerceTeam_EasyCheckout_Block_Onepage_Abstract
{
    const POSITION_MODE_DEFAULT = EcommerceTeam_EasyCheckout_Helper_Easy::POSITION_MODE_DEFAULT;
    const POSITION_MODE_CART    = EcommerceTeam_EasyCheckout_Helper_Easy::POSITION_MODE_CART;

    /** @var Mage_Cms_Block_Block */
    protected $_cmsBlock;
    /** @var bool */
    protected $_isModernSkin;
    /** @var string Mode of checkout view, 'default' or 'cart' */
    protected $_mode;

    /**
     * @return Mage_Cms_Block_Block|null
     */
    public function getCmsBlock()
    {
        if (is_null($this->_cmsBlock)) {
            $blockId = $this->_helper->getConfigData('options/cms_block');
            if ($blockId) {
                $this->_cmsBlock = $this->getLayout()->createBlock('cms/block');
                $this->_cmsBlock->setData('block_id', $blockId);
            }
        }
        return $this->_cmsBlock;
    }

    /**
     * @return string
     */
    public function getCmsBlockHtml()
    {
        $html     = '';
        $cmsBlock = $this->getCmsBlock();
        if ($cmsBlock && $cmsBlock instanceof Mage_Cms_Block_Block) {
            $html = $cmsBlock->toHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getSkin()
    {
        /** @var $helper EcommerceTeam_EasyCheckout_Helper_Easy */
        $helper = Mage::helper('ecommerceteam_easycheckout/easy');
        return $helper->getSkin();
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->_helper->getCheckoutMode();
    }

    /**
     * @return bool
     */
    public function isModernSkin()
    {
        if (is_null($this->_isModernSkin)) {
            $this->_isModernSkin = false !== strpos($this->getSkin(), 'modern');
        }
        return $this->_isModernSkin;
    }

    /**
     * @return EcommerceTeam_EasyCheckout_Block_Checkout
     */
    protected function _beforeToHtml()
    {
        switch ($this->getLayoutMode()) {
            case EcommerceTeam_EasyCheckout_Model_System_Conf_Source_Mode::MODE_THREE_COLUMNS:
                $this->setTemplate('ecommerceteam/echeckout/checkout-3columns.phtml');
                break;
            case EcommerceTeam_EasyCheckout_Model_System_Conf_Source_Mode::MODE_THREE_COLUMNS_V2:
                $this->setTemplate('ecommerceteam/echeckout/checkout-3columns-v2.phtml');
                break;
        }
        return $this;
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        /** @var $headBlock Mage_Page_Block_Html_Head */
        $headBlock = $this->getLayout()->getBlock('head');
        $headBlock->addCss(sprintf('css/ecommerceteam/echeckout/%s.css', $this->getSkin()));

        if ($this->isModernSkin()) {
            $headBlock->addJs('ecommerceteam/easycheckout2/modern.js');
        }

        return parent::_prepareLayout();
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = array())
    {
        $route = explode('/', $route);
        $route[0] = 'checkout';
        $route[1] = 'onepage';
        return parent::getUrl(implode('/', $route), $params = array());
    }

    /**
     * @return string
     */
    public function getReviewBlock()
    {
        if (self::POSITION_MODE_CART == $this->getMode()) {
            return '';
        }
        return $this->getChildHtml('review');
    }

    /**
     * @return string
     */
    public function getTotalsBlock()
    {
        if (self::POSITION_MODE_CART == $this->getMode()) {
            return $this->getChildHtml('totals');
        }
        return '';
    }
}
