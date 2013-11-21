<?php
/**
 * Easy Checkout - Magento Extension
 *
 * @package:    EasyCheckout
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2013 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.0.2
 */

class EcommerceTeam_EasyCheckout_Model_Type_Easy
{
    const METHOD_GUEST    = 'guest';
    const METHOD_REGISTER = 'register';
    const METHOD_CUSTOMER = 'customer';

    /** @var Mage_Checkout_Model_Session */
    protected $_session;
    /** @var Mage_Customer_Model_Session */
    protected $_customerSession;
    /** @var Mage_Sales_Model_Quote */
    protected $_quote;
    /** @var EcommerceTeam_EasyCheckout_Helper_Easy */
    protected $_helper;

    public function __construct()
    {
        $this->_session         = Mage::getSingleton('checkout/session');
        $this->_customerSession = Mage::getSingleton('customer/session');
        $this->_quote           = $this->_session->getQuote();
        $this->_helper          = Mage::helper('ecommerceteam_easycheckout/easy');
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * @return EcommerceTeam_EasyCheckout_Helper_Easy
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return string
     */
    public function getDefaultCountryId()
    {
        return $this->getHelper()->getDefaultCountryId();
    }

    /**
     * @param int $addressId
     * @return Mage_Core_Model_Abstract|Mage_Customer_Model_Address
     * @throws Mage_Core_Exception
     */
    public function getCustomerAddress($addressId) {
        /** @var $address Mage_Customer_Model_Address */
        $address = Mage::getModel('customer/address');
        $address->load($addressId);

        if (!$address->getId() && $address->getId() != $this->getQuote()->getCustomerId()) {
            Mage::throwException($this->getHelper()->__('Customer address not found.'));
        }

        return $address;
    }

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        /** @var $address Mage_Sales_Model_Quote_Address */
        $address = $this->getQuote()->getBillingAddress();
        if (is_null($address->getCountryId())) {
            $address->setCountryId($this->getDefaultCountryId());
        }
        return $address;
    }

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        /** @var $address Mage_Sales_Model_Quote_Address */
        $address = $this->getQuote()->getShippingAddress();
        if (is_null($address->getCountryId())) {
            $address->setCountryId($this->getDefaultCountryId());
        }
        return $address;
    }

    /**
     * @param Varien_Object $data
     * @param int|null $customerAddressId
     * @throws Mage_Core_Exception
     * @return bool|array
     */
    public function saveBillingAddress(Varien_Object $data, $customerAddressId = null)
    {
        /** @var $address Mage_Sales_Model_Quote_Address */
        $address = $this->getBillingAddress();
        if ($customerAddressId) {
            $customerAddress = $this->getCustomerAddress($customerAddressId);
            $address->importCustomerAddress($customerAddress);
        } else {
            $address->addData($data->toArray());
        }
        $address->implodeStreetAddress();
        $validateResults = $address->validate();
        $customerResult  = $this->_validateCustomerData($address->getData());

        if (true != $customerResult) {
            if (true != $validateResults) {
                $validateResults = array_merge($validateResults, $customerResult);
            } else {
                $validateResults = $customerResult;
            }
        }

        return $validateResults;
    }

    /**
     * @param Varien_Object $data
     * @param int|null $customerAddressId
     * @throws Mage_Core_Exception
     * @return bool|array
     */
    public function saveShippingAddress(Varien_Object $data, $customerAddressId = null)
    {
        /** @var $address Mage_Sales_Model_Quote_Address */
        $address = $this->getShippingAddress();
        if ($customerAddressId) {
            $customerAddress = $this->getCustomerAddress($customerAddressId);
            $address->importCustomerAddress($customerAddress);
        } else {
            $address->addData($data->toArray());
        }

        $address->implodeStreetAddress();
        //$address->setCollectShippingRates(true);
        $this->_initSingleShippingMethod($address);

        $validateResults = $address->validate();

        return $validateResults;
    }

    /**
     * @param string $shippingMethodCode
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    public function saveShippingMethod($shippingMethodCode)
    {
        $this->getShippingAddress()->setShippingMethod($shippingMethodCode);
        return $this;
    }

    /**
     * @return bool
     */
    public function validateShippingMethod()
    {
        if (!$this->_quote->isVirtual()) {
            $address = $this->getShippingAddress();
            $method  = $address->getShippingMethod();
            if (!$method || !$address->getShippingRateByCode($method)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $paymentMethodData
     * @throws Mage_Core_Exception
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    public function savePaymentMethod($paymentMethodData)
    {
        if ($paymentMethodData
            && is_array($paymentMethodData)
            && !empty($paymentMethodData)) {
            $this->getQuote()->getPayment()->importData($paymentMethodData);
        } else {
            Mage::throwException($this->getHelper()->__('Invalid payment data.'));
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getCheckoutMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * @param $method
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    public function saveCheckoutMethod($method)
    {
        if (self::METHOD_GUEST == $method) {
            if (!$this->_helper->isAllowedGuestCheckout()) {
                Mage::throwException($this->_helper->__('Guest checkout not allowed!'));
            }
        }
        $this->getQuote()->setCheckoutMethod($method);
        return $this;
    }

    /**
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    public function commit()
    {
        $this->_quote->collectTotals()->save();
        return $this;
    }

    /**
     * Place order
     *
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     * @throws EcommerceTeam_EasyCheckout_Exception
     */
    public function saveOrder()
    {
        $checkoutMethod = $this->getCheckoutMethod();
        switch ($checkoutMethod) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                $this->_checkCustomerAlreadyExists();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $this->_checkCustomerAlreadyExists();
                break;
            case self::METHOD_CUSTOMER:
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        $quote = $this->_quote;
        $quote->collectTotals();

        /** @var $service Mage_Sales_Model_Service_Quote */
        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        /** @var $order Mage_Sales_Model_Order */
        $order   = $service->getOrder();

        if (self::METHOD_REGISTER == $checkoutMethod) {
            try {
                $this->_involveNewCustomer();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $this->_session->setData('last_quote_id', $quote->getId());
        $this->_session->setData('last_success_quote_id', $quote->getId());
        $this->_session->clearHelperData();

        if ($order) {
            Mage::dispatchEvent('checkout_type_onepage_save_order_after',
                array('order' => $order, 'quote' => $quote));
            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $quote->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
            // add order information to the session
            $this->_session->addData(
                array(
                    'last_order_id'      => $order->getId(),
                    'redirect_url'       => $redirectUrl,
                    'last_real_order_id' => $order->getIncrementId(),
                )
            );
        }

        // add recurring profiles information to the session
        $profiles = $service->getRecurringPaymentProfiles();

        if ($profiles) {
            $ids = array();
            foreach ($profiles as $profile) {
                /** @var $profile Mage_Core_Model_Abstract*/
                $ids[] = $profile->getId();
            }
            $this->_session->setData('last_recurring_profile_ids', $ids);
        }

        Mage::dispatchEvent(
            'checkout_submit_all_after',
            array('order' => $order, 'quote' => $quote, 'recurring_profiles' => $profiles)
        );
        return $this;
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    protected function _prepareGuestQuote()
    {
        $quote = $this->_quote;
        $quote->unsetData('customer_id');
        $quote->addData(
            array(
                'customer_is_guest' => true,
                'customer_email'    => $quote->getBillingAddress()->getData('email'),
                'customer_group_id' => Mage_Customer_Model_Group::NOT_LOGGED_IN_ID
            )
        );
        return $this;
    }

    /**
     * Prepare quote for customer registration and customer order submit
     *
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    protected function _prepareNewCustomerQuote()
    {
        $quote           = $this->_quote;
        /** @var $billingAddress Mage_Sales_Model_Quote_Address */
        $billingAddress  = $quote->getBillingAddress();
        /** @var $shippingAddress Mage_Sales_Model_Quote_Address */
        $shippingAddress = $quote->isVirtual() ? null : $quote->getShippingAddress();
        /* @var $customer Mage_Customer_Model_Customer */
        $customer        = $quote->getCustomer();
        /** @var $customerBilling Mage_Customer_Model_Address */
        $customerBilling = $billingAddress->exportCustomerAddress();
        $billingAddress->setData('customer_address', $customerBilling);
        $customer->addAddress($customerBilling);
        $customerBilling->setData('is_default_billing', true);
        if ($shippingAddress && !$shippingAddress->getSameAsBilling()) {
            /** @var $customerShipping Mage_Customer_Model_Address */
            $customerShipping = $shippingAddress->exportCustomerAddress();
            $shippingAddress->setData('customer_address', $customerShipping);
            $customer->addAddress($customerShipping);
            $customerShipping->setData('is_default_shipping', true);
        } else {
            $customerBilling->setData('is_default_shipping', true);
        }

        $this->_helper->copyFieldset('checkout_onepage_quote', 'to_customer', $quote, $customer);

        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));

        $quote->setCustomer($customer);
        $quote->setCustomerId(true);
        return $this;
    }

    /**
     * Prepare quote for customer order submit
     *
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    protected function _prepareCustomerQuote()
    {
        $quote      = $this->_quote;
        /** @var $billingAddress Mage_Sales_Model_Quote_Address */
        $billingAddress    = $quote->getBillingAddress();
        /** @var $shippingAddress Mage_Sales_Model_Quote_Address */
        $shippingAddress   = $quote->isVirtual() ? null : $quote->getShippingAddress();
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $quote->getCustomer();
        if (!$billingAddress->getCustomerId()
            || $billingAddress->getSaveInAddressBook()
            || !$customer->getDefaultBillingAddress()
        ) {
            $customerBilling = $billingAddress->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billingAddress->setData('customer_address', $customerBilling);
        }
        if ($shippingAddress && !$shippingAddress->getSameAsBilling() &&
            (!$shippingAddress->getCustomerId()
                || $shippingAddress->getSaveInAddressBook()
                || !$customer->getDefaultShippingAddress()
            )) {
            $customerShipping = $shippingAddress->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shippingAddress->setData('customer_address', $customerShipping);
        }

        if (isset($customerBilling) && !$customer->hasData('default_billing')) {
            $customerBilling->setData('is_default_billing', true);
        }
        if ($shippingAddress && isset($customerShipping) && !$customer->hasData('default_shipping')) {
            $customerShipping->setData('is_default_shipping', true);
        } else if (isset($customerBilling) && !$customer->hasData('default_shipping')) {
            $customerBilling->setData('is_default_shipping', true);
        }
        $quote->setCustomer($customer);
        return $this;
    }

    /**
     * Involve new customer to system
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _involveNewCustomer()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote    = $this->_quote;
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $quote->getCustomer();
        /** @var $customerHelper Mage_Customer_Helper_Data */
        $customerHelper  = Mage::helper('customer');
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');

        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', '', $quote->getStoreId());
            $url = $customerHelper->getEmailConfirmationUrl($customer->getData('email'));
            $customerSession->addSuccess(
                $customerHelper->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url)
            );
        } else {
            $customer->sendNewAccountEmail('registered', '', $quote->getStoreId());
            $customerSession->loginById($customer->getId());
        }
        return $this;
    }

    /**
     * Validate customer data and set some its data for further usage in quote
     * Will return either true or array with error messages
     *
     * @param array $data
     * @return true|array
     */
    protected function _validateCustomerData(array $data)
    {
        $quote = $this->_quote;
        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setFormCode('checkout_register');
        $customerForm->setIsAjaxRequest(true);

        if ($quote->getCustomerId()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer     = $quote->getCustomer();
            $customerForm->setEntity($customer);
            /** @var $customerData array */
            $customerData = $customer->getData();
        } else {
            /* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer');
            $customerForm->setEntity($customer);
            $customerRequest = $customerForm->prepareRequest($data);
            $customerData    = $customerForm->extractData($customerRequest);
        }

        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            return array(
                'error'     => -1,
                'message'   => implode(', ', $customerErrors)
            );
        }

        if ($quote->getCustomerId()) {
            return true;
        }

        $customerForm->compactData($customerData);

        if (self::METHOD_REGISTER == $this->getCheckoutMethod()) {
            // set customer password
            $customer->setPassword(isset($data['password']) ? $data['password'] : null);
            $customer->setConfirmation(isset($data['confirmation']) ? $data['confirmation'] : null);
        } else {
            // emulate customer password for quest
            $password = $customer->generatePassword();
            $customer->setPassword($password);
            $customer->setConfirmation($password);
        }

        $result = $customer->validate();
        if (true !== $result && is_array($result)) {
            return array(
                'error'   => -1,
                'message' => implode(', ', $result)
            );
        }

        if (self::METHOD_REGISTER == $this->getCheckoutMethod()) {
            // save customer encrypted password in quote
            $quote->setPasswordHash($customer->encryptPassword($customer->getPassword()));
        }

        // copy customer/guest email to address
        $quote->getBillingAddress()->setData('email', $customer->getData('email'));

        // copy customer data to quote
        $this->_helper->copyFieldset('customer_account', 'to_quote', $customer, $quote);

        return true;
    }

    /**
     * @return bool
     * @throws EcommerceTeam_EasyCheckout_Exception
     */
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

    /**
     * Initialize default options
     */
    public function initCheckout()
    {
        $session         = $this->getSession();
        $customerSession = $this->getCustomerSession();
        $quote           = $this->getQuote();
        $helper          = $this->getHelper();
        $session->setData('checkout_state', Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN);

        if ($customerSession->isLoggedIn() && !$session->hasData('customer_loaded')) {
            $quote->assignCustomer($customerSession->getCustomer());
            $session->setData('customer_loaded', true);
        }

        try {
            if ($payment = $helper->getDefaultPaymentMethod()) {
                $quote->getPayment()->importData(array('method' => $payment->getData('code')));
            }
        } catch(Exception $e) {
            // Ignore errors
        }

        $geoIpRecord = $helper->getGeoipRecord();
        if ($geoIpRecord) {
            $city        = $helper->getConfigData('options/geoip_city')  ? $geoIpRecord->city : '';
            $postCode    = $helper->getConfigData('options/geoip_post')  ? $geoIpRecord->postal_code : '';
            $regionCode  = $helper->getConfigData('options/geoip_state') ? $geoIpRecord->region : '';
            $countryCode = $geoIpRecord->country_code;
            $regionId    = false;

            if ($regionCode) {
                /** @var $directory Mage_Directory_Model_Region */
                $directory = Mage::getModel('directory/region');
                $region    = $directory->loadByCode($regionCode, $countryCode);

                if ($region) {
                    $regionId = $region->hasData('region_id') ? $region->getData('region_id') : '';
                }
            }

            $address = $this->getQuote()->getBillingAddress();
            if (!$address->getCountryId() && $countryCode) {
                $address->setCountryId($countryCode);
            }
            if (!$address->getCity() && $city) {
                $address->setCity($city);
            }
            if (!$address->getPostcode() && $postCode) {
                $address->setPostcode($postCode);
            }
            if (!$address->getRegionId() && $regionId) {
                $address->setRegionId($regionId);
            }

            if (!$quote->isVirtual()) {
                $address = $quote->getShippingAddress();
                if (!$address->getCountryId() && $countryCode) {
                    $address->setCountryId($countryCode);
                }
                if (!$address->getCity() && $city) {
                    $address->setCity($city);
                }
                if (!$address->getPostcode() && $postCode) {
                    $address->setPostcode($postCode);
                }
                if (!$address->getRegionId() && $regionId) {
                    $address->setRegionId($regionId);
                }
            }
        }

        $quote->collectTotals();
        if (!$quote->isVirtual()) {
            $address = $quote->getShippingAddress();
            if (!$address->getShippingMethod()) {
//                $address->setCollectShippingRates(true);
//                $address->collectShippingRates();
                $this->_initSingleShippingMethod($address);
                $quote->setData('totals_collected_flag', false);
                $quote->collectTotals();
            } else {
//                $address->setCollectShippingRates(true);
            }
        }
        $quote->save();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     */
    protected function _initSingleShippingMethod(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setCollectShippingRates(true);
        $address->collectShippingRates();
        $rates = $address->getGroupedAllShippingRates();
        if (count($rates) == 1) {
            foreach ($rates as $methods) {
                if (count($methods) == 1) {
                    /** @var $method Mage_Core_Model_Abstract */
                    foreach($methods as $method){
                        $address->setShippingMethod($method->getData('code'));
                    }
                }
                break;
            }
        }
        return $this;
    }

    /**
     * @return Mage_Checkout_Model_Session
     * @deprecated please use getSession()
     */
    public function getCheckout()
    {
        return $this->_session;

    }

    /**
     * @param array $paymentMethodData
     * @return EcommerceTeam_EasyCheckout_Model_Type_Easy
     * @deprecated please use savePaymentMethod()
     */
    public function savePayment($paymentMethodData)
    {
        return $this->savePaymentMethod($paymentMethodData);
    }
}
