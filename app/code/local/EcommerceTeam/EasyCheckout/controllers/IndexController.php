<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_IndexController
    extends Mage_Core_Controller_Front_Action
{
    /** @var $_helper EcommerceTeam_EasyCheckout_Helper_Easy */
    protected $_helper;
    /** @var $_session Mage_Customer_Model_Session */
    protected $_customerSession;
    /** @var $_checkout Mage_Checkout_Model_Session */
    protected $_checkoutSession;
    /** @var $_checkoutModel EcommerceTeam_EasyCheckout_Model_Type_Easy */
    protected $_checkoutModel;
    /** @var Mage_Sales_Model_Quote */
    protected $_quote;
    /**
     * @var string
     */
    protected $_unknownErrorMsg = 'There was an error processing your order. Please contact us or try again later.';

    /**
     * @return Varien_Object
     */
    protected function _getResult()
    {
        return new Varien_Object(array('error' => false, 'message' => array(), 'message_position' => false));
    }

    /**
     * @return EcommerceTeam_EasyCheckout_IndexController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_helper          = Mage::helper('ecommerceteam_easycheckout/easy');
        $this->_customerSession = Mage::getSingleton('customer/session');
        $this->_checkoutModel   = Mage::getSingleton('ecommerceteam_easycheckout/type_easy');
        $this->_checkoutSession = $this->_checkoutModel->getSession();
        $this->_quote           = $this->_checkoutModel->getQuote();

        if ($this->_quote->getIsMultiShipping()) {
            $this->_quote->setIsMultiShipping(false);
            $this->_quote->removeAllAddresses();
        }

        return $this;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, $params = array())
    {
        return Mage::getUrl($route, $params);
    }

    /**
     * @return EcommerceTeam_EasyCheckout_IndexController|Mage_Core_Controller_Varien_Action
     */
    public function addActionLayoutHandles()
    {
        $availableActions = array('index');
        if (in_array($this->getRequest()->getActionName(), $availableActions)) {
            $update = $this->getLayout()->getUpdate();

            // load store handle
            $update->addHandle('STORE_'.Mage::app()->getStore()->getCode());

            // load theme handle
            $package = Mage::getSingleton('core/design_package');
            $update->addHandle(
                'THEME_'.$package->getArea().'_'.$package->getPackageName().'_'.$package->getTheme('layout')
            );

            // load action handle
            $update->addHandle('ecommerceteam_easycheckout');
            /** @var $sagePayConfig SimpleXMLElement */
            $sagePayConfig = Mage::getConfig()->getModuleConfig('Ebizmarts_SagePaySuite');
            if ($sagePayConfig && true == $sagePayConfig->active) {
                $update->addHandle('ecommerceteam_easycheckout_sagepay');
            }

            return $this;
        }
        return parent::addActionLayoutHandles();
    }

    /**
     * Checkout Form
     *
     * @return mixed
     */
    public function indexAction()
    {
        $quote = $this->_quote;
        if (!$quote->hasItems() || $quote->getData('has_error')) {
            $this->_redirect('checkout/cart');
            return;
        }

        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            $this->_checkoutSession->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }

        $this->_checkoutSession->setData('cart_was_updated', false);
        $this->_customerSession->setBeforeAuthUrl(
            $this->getUrl('*/*/*', array('_secure' => true))
        );

        $this->_checkoutModel->initCheckout();


        $this->loadLayout();
        $this->_initLayoutMessages(array('customer/session', 'checkout/session'));
        /** @var $headBlock Mage_Page_Block_Html_Head */
        $headBlock = $this->getLayout()->getBlock('head');
        $headBlock->setData('title', $this->_helper->getConfigData('options/title'));
        $this->renderLayout();
    }

    /**
     * @return string
     */
    protected function _initializeCheckoutMethod()
    {
        if ($this->_customerSession->isLoggedIn()) {
            $checkoutMethod = EcommerceTeam_EasyCheckout_Model_Type_Easy::METHOD_CUSTOMER;
        } else if ($this->getRequest()->getPost('create_account') || !$this->_helper->isAllowedGuestCheckout()) {
            $checkoutMethod = EcommerceTeam_EasyCheckout_Model_Type_Easy::METHOD_REGISTER;
        } else {
            $checkoutMethod = EcommerceTeam_EasyCheckout_Model_Type_Easy::METHOD_GUEST;
        }
        $this->_checkoutModel->saveCheckoutMethod($checkoutMethod);
        return $checkoutMethod;
    }

    /**
     * Save customer address and update checkout form
     */
    public function saveAddressAction()
    {
        $result             = $this->_getResult();
        $billingAddressData = new Varien_Object($this->getRequest()->getPost('billing'));
        $billingAddressId   = $this->getRequest()->getPost('billing_address_id');
        $ignoreErrors       = (bool) !$this->getRequest()->getPost('is_final');
        $checkoutMethod     = $this->_initializeCheckoutMethod();
//print_r($billingAddressData);
//print_r("Id: " .$billingAddressId);
//die();
        $billingAddressResult = $this->_checkoutModel->saveBillingAddress(
            $billingAddressData,
            $billingAddressId
        );

        if (!$this->_quote->getIsVirtual()) {
            $shippingAsBilling = (!$this->_helper->differentShippingEnabled() || $billingAddressData->getData('use_for_shipping') ? true : false);
            if ($shippingAsBilling) {
                $shippingAddressResult = $this->_checkoutModel->saveShippingAddress(
                    $billingAddressData,
                    $billingAddressId
                );
            } else {
                $shippingAddressData   = new Varien_Object($this->getRequest()->getPost('shipping'));
                $shippingAddressId     = $this->getRequest()->getPost('shipping_address_id');
                $shippingAddressResult = $this->_checkoutModel->saveShippingAddress(
                    $shippingAddressData,
                    $shippingAddressId
                );
            }
        }

        if ($this->getRequest()->getParam('is_final')) {
            if (EcommerceTeam_EasyCheckout_Model_Type_Easy::METHOD_REGISTER == $checkoutMethod
                || EcommerceTeam_EasyCheckout_Model_Type_Easy::METHOD_GUEST == $checkoutMethod
            ) {
                try {
                    $this->_checkCustomerAlreadyExists();
                } catch (EcommerceTeam_EasyCheckout_Exception $e) {
                    $result->setData('error', true);
                    $result->setData('message', array_merge($result->getData('message'), array($e->getMessage())));
                } catch (Exception $e) {
                    Mage::logException($e);
                    $result->setData('error', true);
                    if ($this->_helper->isDeveloperMode()) {
                        $result->setData('message', $e->getMessage());
                    } else {
                        $result->setData('message', $this->_helper->__($this->_unknownErrorMsg));
                    }
                }
            }
        }
		$this->_quote->getShippingAddress()->setCollectShippingRates(true);
        $this->_checkoutModel->commit();

        if (!$ignoreErrors) {
            if (true !== $billingAddressResult) {
                $result->setData('error', true);
                $result->setData('message', array_merge($result->getData('message'), $billingAddressResult));
            }
            if (!$this->_quote->getIsVirtual()) {
                if (isset($shippingAddressResult) && true !== $shippingAddressResult) {
                    $result->setData('error', true);
                    $result->setData('message', array_merge($result->getData('message'), $shippingAddressResult));
                }
            }
        }

        if (!$this->getRequest()->getParam('is_final')) {
            $result->addData(
                $this->_getBlocksHtml(array('shipping_method_html', 'payment_method_html', 'review_html'))
            );
        }

        $this->getResponse()->appendBody($result->toJson());
    }

    /**
     * Save shipping method, update payment and review block
     */
    public function saveShippingMethodAction()
    {
        $result         = $this->_getResult();
        $ignoreErrors   = (bool) !$this->getRequest()->getPost('is_final');
        try {
            $shippingMethod = $this->getRequest()->getPost('shipping_method', false);
            $this->_checkoutModel->saveShippingMethod($shippingMethod);
            Mage::dispatchEvent(
                'easycheckout_save_shipping_method_after', array('quote' => $this->_checkoutModel->getQuote())
            );
            $this->_checkoutModel->commit();

            if (!$this->getRequest()->getParam('is_final')) {
                $result->addData(
                    $this->_getBlocksHtml(array('payment_method_html', 'review_html'))
                );
            }

            if (!$ignoreErrors) {
                if (!$this->_checkoutModel->validateShippingMethod()) {
                    Mage::throwException($this->__('Please specify shipping method.'));
                }
            }
        } catch (Mage_Core_Exception $e) {
            if (!$ignoreErrors) {
                $result->setData('error', true);
                $result->setData('message', $e->getMessage());
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $result->setData('error', true);
            if ($this->_helper->isDeveloperMode()) {
                $result->setData('message', $e->getMessage());
            } else {
                $result->setData('message', $this->_helper->__($this->_unknownErrorMsg));
            }
        }

        $this->getResponse()->appendBody($result->toJson());
    }

    /**
     * Save payment method and update review block
     */
    public function savePaymentMethodAction()
    {
        $this->getRequest()->setActionName('savePayment');
        $result         = $this->_getResult();
        $ignoreErrors   = (bool) !$this->getRequest()->getPost('is_final');
        try {
            /** @var $paymentMethodData array */
            $paymentMethodData = $this->getRequest()->getPost('payment', false);
            $this->_checkoutModel->savePaymentMethod($paymentMethodData);
            $this->_checkoutModel->commit();
            if ($this->getRequest()->getParam('is_final')) {
                $redirectUrl = $this->_quote->getPayment()->getCheckoutRedirectUrl();
                if ($redirectUrl) {
                    $result->setData('redirect_url', $redirectUrl);
                } else {
                    $result->setData('review_after_html', $this->_getReviewAfterHtml());
                }
            } else {
                $result->addData(
                    $this->_getBlocksHtml(array('review_html'))
                );
            }
        } catch (Mage_Core_Exception $e) {
            if (!$ignoreErrors) {
                $result->setData('error', true);
                $result->setData('message', $e->getMessage());
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $result->setData('error', true);
            if ($this->_helper->isDeveloperMode()) {
                $result->setData('message', $e->getMessage());
            } else {
                $result->setData('message', $this->_helper->__($this->_unknownErrorMsg));
            }
        }
        $this->getResponse()->appendBody($result->toJson());
    }

    /**
     * Apply or remove coupon code.
     *
     * @return mixed
     */
    public function saveCouponCodeAction()
    {
        $result  = $this->_getResult();
        $request = $this->getRequest();
        $quote   = $this->_quote;
        if (!$quote->getItemsCount()) {
            return;
        }
        
        //Messaggi in basso
        $result->setData('message_position', true);
        
        $couponCode = $request->getParam('coupon-code');
        if ($request->getParam('remove-coupon') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $quote->getCouponCode();
        try {
            if (!strlen($couponCode) && !strlen($oldCouponCode)) {
                Mage::throwException($this->_helper->__('Coupon code is not valid.'));
            }
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode(strlen($couponCode) ? $couponCode : '');
            $quote->collectTotals();
            $quote->save();
            if ($couponCode) {
                if ($couponCode == $quote->getCouponCode()) {
                    $result->setData(
                        'success',
                        $this->_helper->__(
                            'Coupon code "%s" was applied successfully.',
                            $this->_helper->escapeHtml($couponCode)
                        )
                    );
                } else {
                    $result->setData(
                        array(
                            'error'   => true,
                            'message' => $this->_helper->__(
                                'Coupon code "%s" is not valid.',
                                $this->_helper->escapeHtml($couponCode)
                            ),
                        )
                    );
                }
            } else {
                $result->setData('success', $this->_helper->__('Coupon code was canceled successfully.'));
            }
            $result->addData(
                $this->_getBlocksHtml(
                    array(
                        'shipping_method_html',
                        'coupon_html',
                        'payment_method_html',
                        'totals_html'
                    ))
            );
        } catch (Mage_Core_Exception $e) {
            $result->setData(
                array(
                    'error'   => true,
                    'message' => $e->getMessage(),
                )
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $result->setData('error', true);
            if ($this->_helper->isDeveloperMode()) {
                $result->setData('message', $e->getMessage());
            } else {
                $result->setData('message', $this->_helper->__($this->_unknownErrorMsg));
            }
        }
        $this->getResponse()->appendBody($result->toJson());
    }

    /**
     * Success order page
     *
     * @return void
     */
    public function successAction()
    {
        if (!$this->_checkoutSession->getData('last_success_quote_id')) {
            $this->_redirect('*/cart');
            return;
        }

        $lastQuoteId = $this->_checkoutSession->getData('last_quote_id');
        $lastOrderId = $this->_checkoutSession->getData('last_order_id');

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('*/cart');
            return;
        }

        $this->_checkoutSession->clear();

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent(
            'checkout_onepage_controller_success_action',
            array(
                'order_ids' => array($lastOrderId)
            )
        );
        $this->renderLayout();
    }

    /**
     * @return void
     */
    public function failureAction()
    {
        $lastQuoteId = $this->_checkoutSession->getData('last_quote_id');
        $lastOrderId = $this->_checkoutSession->getData('last_order_id');

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('*/cart');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Place order and payment
     */
    public function saveOrderAction()
    {
        $request = $this->getRequest();
        $result  = $this->_getResult();
        /** @var $checkoutHelper Mage_Checkout_Helper_Data */
        $checkoutHelper  = Mage::helper('checkout');
        if ($request->isPost()) {
            try {
                if (!$this->_quote->hasItems() || $this->_quote->getHasError()){
                    $result->setData('redirect_url', $this->getUrl('checkout/cart'));
                    throw new EcommerceTeam_EasyCheckout_Exception($this->_helper->__('Sorry, error occurred while placing the order.'));
                }

                if ($requiredAgreements = $checkoutHelper->getRequiredAgreementIds()) {
                    $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                    $diff = array_diff($requiredAgreements, $postedAgreements);
                    if (!empty($diff)) {
                        Mage::throwException($this->__('Please agree to all Terms and Conditions before placing the order.'));
                    }
                }

                $this->_checkoutModel->savePaymentMethod($request->getPost('payment'));
                $this->_checkoutModel->saveOrder();
                $this->_quote->save();

                if ($request->getParam('subscribe', false)) {
                    if ($this->_customerSession->isLoggedIn()) {
                        $email = $this->_customerSession->getCustomer()->getEmail();
                    } else {
                        $email = $this->_quote->getBillingAddress()->getEmail();
                    }
                    /** @var $newsletterModel Mage_Newsletter_Model_Subscriber */
                    $newsletterModel = Mage::getModel('newsletter/subscriber');
                    $newsletterModel->subscribe($email);
                }

                $redirectUrl = $this->_checkoutSession->getData('redirect_url');
                if ($redirectUrl) {
                    $result->setData('redirect_url', $redirectUrl);
                }
                $result->setData('success', true);
            } catch (Mage_Core_Exception $e) {
                $result->addData(
                    array(
                        'error' => true,
                        'message' => $e->getMessage(),
                    )
                );
                $checkoutHelper->sendPaymentFailedEmail($this->_quote, $e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $result->setData('error', true);
                if ($this->_helper->isDeveloperMode()) {
                    $result->setData('message', $e->getMessage());
                } else {
                    $result->setData('message', $this->_helper->__($this->_unknownErrorMsg));
                }
                $checkoutHelper->sendPaymentFailedEmail($this->_quote, $e->getMessage());
            }
        } else {
            $result->setData(
                array(
                    'error'   => true,
                    'message' => $this->_helper->__('Invalid request.'),
                )
            );
        }
        $this->getResponse()->appendBody($result->toJson());
    }

    /**
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('core/layout');
        $layout->getUpdate()->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getOutput();
    }

    /**
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('core/layout');
        $layout->getUpdate()->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getOutput();
    }

    /**
     * @return string
     */
    protected function _getAdditionalHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('core/layout');
        $layout->getUpdate()->load('checkout_onepage_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getOutput();
    }

    /**
     * @return string
     */
    protected function _getReviewHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('core/layout');
        $layout->getUpdate()->load('ecommerceteam_echeckout_review');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getOutput();
    }

    protected function _getTotalsHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('core/layout');
        $layout->getUpdate()->load('ecommerceteam_echeckout_totals');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getOutput();
    }

    /**
     * @return string
     */
    protected function _getReviewAfterHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('core/layout');
        $layout->getUpdate()->load('checkout_onepage_review');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getBlock('checkout.onepage.review.info.items.after')->toHtml();
    }

    /**
     * @return string
     */
    protected function _getCouponHtml()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('core/layout');
        $layout->getUpdate()->load('ecommerceteam_echeckout_onepage_coupon');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getOutput();
    }

    /**
     * Customer login
     */
    public function loginAction()
    {
        $result   = $this->_getResult();
        $username = $this->getRequest()->getPost('username');
        $password = $this->getRequest()->getPost('password');

        if($username && $password){
            try {
                $this->_customerSession->login($username, $password);
            } catch (Mage_Core_Exception $e) {
                switch ($e->getCode()) {
                    case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                        $message = $this->_helper->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', Mage::helper('customer')->getEmailConfirmationUrl($username));
                        break;
                    default:
                        $message = $e->getMessage();
                        break;
                }
                $result->addData(
                    array(
                        'error'   => true,
                        'message' => $message
                    )
                );

            } catch(Exception $e) {
                Mage::logException($e);
                $result->setData('error', true);
                if ($this->_helper->isDeveloperMode()) {
                    $result->setData('message', $e->getMessage());
                } else {
                    $result->setData('message', $this->_helper->__($this->_unknownErrorMsg));
                }
            }
        } else {
            $result->addData(
                array(
                    'error'   => true,
                    'message' => $this->_helper->__('Login and password are required')
                )
            );
        }
        $this->getResponse()->appendBody($result->toJson());
    }

    protected function _checkCustomerAlreadyExists()
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer  = Mage::getModel('customer/customer');
        $email     = $this->_quote->getCustomerEmail();
        $websiteId = Mage::app()->getWebsite()->getId();

        if($websiteId){
            $customer->setData('website_id', $websiteId);
        }

        if($customer->loadByEmail($email)->getId() > 0){
            throw new EcommerceTeam_EasyCheckout_Exception(
                $this->_helper->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address.')
            );
        }

        return false;
    }

    protected function _getBlocksHtml($blockNames = array())
    {
        $result = array();
        $mode   = $this->getRequest()->getParam('mode');
        if (in_array('shipping_method_html', $blockNames) && $this->_helper->canShip()) {
            $result['shipping_method_html'] = $this->_getShippingMethodsHtml();
        }
        if (in_array('payment_method_html', $blockNames)) {
            $result['payment_method_html'] = $this->_getPaymentMethodsHtml();
        }
        if (in_array('review_html', $blockNames)) {
            if (EcommerceTeam_EasyCheckout_Block_Checkout::POSITION_MODE_CART == $mode) {
                $result['totals_html'] = $this->_getTotalsHtml();
            } else {
                $result['review_html'] = $this->_getReviewHtml();
            }
        }
        
        if (in_array('totals_html', $blockNames) && !in_array('review_html', $blockNames)) {
            $result['totals_html'] = $this->_getTotalsHtml();
        }
        
        if (in_array('coupon_html', $blockNames)) {
            $result['coupon_html'] = $this->_getCouponHtml();
        }
        return $result;
    }
    
    public function loadaddressAction() {
        
        $addressId = $this->getRequest()->getPost("id");
        
        $address = null;
        $data = new Varien_Object(array('error'));
        
        if ($addressId > 0)
            $address = Mage::getModel('customer/address')->Load($addressId);
        
        if (!is_null($address )) {
            $data = $address->toJson();
        }
        
        $this->getResponse()->appendBody($data);
        
    }
}