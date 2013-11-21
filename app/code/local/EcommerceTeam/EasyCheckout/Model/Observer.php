<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Model_Observer
    extends EcommerceTeam_Core_Model_Observer_Abstract
{
    protected $_session;
    protected $_checkout;
    protected $_helper;

    /**
     * @return string
     */
    protected function _getExtensionName()
    {
        return 'EcommerceTeam_EasyCheckout';
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        if (is_null($this->_session)) {
            $this->_session = Mage::getSingleton('customer/session');
        }
        return $this->_session;
    }

    /**
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Save advanced customer and order data
     */
    public function saveCheckoutAdvancedData()
    {
        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = Mage::getSingleton('checkout/session');
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = $this->getCustomerSession();
        if($this->getRequest()->getParam('subscribe', false)){
            if($customerSession->isLoggedIn()){
                /** @var $customer Mage_Customer_Model_Customer */
                $customer = $customerSession->getCustomer();
                $email    = $customer->getData('email');
            }else{
                $email    = $checkoutSession->getQuote()->getBillingAddress()->getEmail();
            }
            /** @var $subscriber Mage_Newsletter_Model_Subscriber */
            $subscriber = Mage::getModel('newsletter/subscriber');
            $subscriber->subscribe($email);
        }
    }

    /**
     * Add custom layout update to cart page
     *
     * @param $observer
     * @return void
     */
    public function addCartLayoutUpdates(Varien_Event_Observer $observer)
    {
        /** @var $helper EcommerceTeam_EasyCheckout_Helper_Easy */
        $helper = Mage::helper('ecommerceteam_easycheckout/easy');
        $quote  = $helper->getQuote();
        /** @var $action Mage_Core_Controller_Varien_Action */
        $action = $observer->getData('action');
        if ($action instanceof Mage_Checkout_CartController && 'index' == $action->getRequest()->getActionName()) {
            if ($quote->hasItems() && !$quote->getData('has_error')) {
                if ($helper->getConfigFlag('options/oncart_enabled')) {
                    $helper->getCheckoutModel()->initCheckout();
                    /** @var $controller Mage_Core_Controller_Varien_Action */
                    $controller = $observer->getData('action');
                    if ('checkout_cart_index' == $controller->getFullActionName()) {
                        $layoutUpdate = $controller->getLayout()->getUpdate();
                        $layoutUpdate->addHandle('ecommerceteam_checkout_cart_index');
                        /** @var $sagePayConfig SimpleXMLElement */
                        $sagePayConfig = Mage::getConfig()->getModuleConfig('Ebizmarts_SagePaySuite');
                        if ($sagePayConfig && true == $sagePayConfig->active) {
                            $layoutUpdate->addHandle('ecommerceteam_easycheckout_sagepay');
                        }
                    }
                }
            }
        }
    }
}
