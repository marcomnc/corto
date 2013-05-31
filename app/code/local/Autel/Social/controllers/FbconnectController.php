<?php


/**
 * Base Facebook Controller
 * 
 * @category   Autel
 * @package    autel_Social
 * @author     Marco Mancinelli 03-06-11
 */

class Autel_Social_FbconnectController extends Mage_Core_Controller_Front_Action
{
    protected $_defaultGroup;
    protected $_errorArray = array(0 => true,
                                   1 => 'Create Account Error',
                                   2 => 'FB Login Fail',
                                   3 => 'Email not valid');
    protected $_storeUrl = '';
    
    public function _construct() {
        $this->_defaultGroup = Mage::getStoreConfig('autelsocial/settings/fb_customer_group', Mage::app()->getStore()->getId());
        if (is_null($this->_defaultGroup) || $this->_defaultGroup == '')
            $this->_defaultGroup = Mage::getStoreConfig('customer/create_account/default_group', Mage::app()->getStore()->getId());
        parent::_construct();
    }
    
    public function indexAction() {

        $params = $this->getRequest()->getParams();   
        $this->_fbLogin();
        $this->_redirectUrl(Mage::helper('core')->urlDecode($params['url']).$this->_storeUrl);
    }
    
    public function ajaxindexAction () {
        
        $params = $this->getRequest()->getParams();  
        $retJson[0] = $this->_errorArray[$this->_fbLogin()];       
        $retJson[1] = Mage::helper('core')->urlDecode($params['url']).$this->_storeUrl;

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($retJson));        
    }
    
    private function _createNewUser ($arrUserInfo) {
        $customerId = 0;
        
        if (array_key_exists('email', $arrUserInfo) && 
            array_key_exists('first_name', $arrUserInfo) && 
            array_key_exists('last_name', $arrUserInfo)) {            
            $customer = Mage::getModel('customer/customer')
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $customer->setEmail($arrUserInfo['email']);
            $customer->setFirstname($arrUserInfo['first_name']);
            $customer->setLastname($arrUserInfo['last_name']);
            $customer->setDob($arrUserInfo['birthday']);
            $customer->setFbUid($arrUserInfo['uid']);
            $customer->setGender($arrUserInfo['gender']);
            $customer->setIsActive(1);
            $customer->setIsSubscribed(1); //Iscritto di defaul alla news letter
            $customer->setGroupId($this->_defaultGroup);
            try {
                $customer->save();
                $customer->setConfirmation(null);
                $customer->save();
                $customerId = $customer->getId();
            } catch (Exception $e) {
                Mage::Log($e, 'ERROR');
            }
        
        }
        return $customerId;    
    }
    
    private function _fbLogin () {
        $retValue = 0;
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $fbConnect = Mage::getModel('autelsocial/fbconnect');

            if ($fbConnect->isFbConnect()) {
//                FA casino perchè cambia il negozio in fase di login
//                if (Mage::getStoreConfig('general/locale/code',Mage::app()->getStore()->getId())!=$fbConnect->getFbUserProfileInfo('locale')) {
//                    $stores = Mage::getModel('core/store')->getCollection();
//                    foreach ($stores as $store) {
//                        $storeLan = Mage::getStoreConfig('general/locale/code',$store->getId());
//                        if (( $storeLan == $fbConnect->getFbUserProfileInfo('locale')) || ($storeLan == 'en_EN' && is_null ($this->_storeUrl))) {
//                            $this->_storeUrl = '?___store='.$store->getCode();
//                        }
//                    }
//                }
                $uid = $fbConnect->getFbMail();
                if (!is_null ($uid)){
                    $customer = Mage::getModel('customer/customer')
                                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                                    ->loadByEmail($uid);
                    if (!is_null ($customer->getId()) || $customer->getId()>0) {
                        if (is_null ($customer->getFbUid()) || $customer->getFbUid() == '') {
                            $customer->setFbUid($fbConnect->getFbUserProfileInfo('id'));          
                            if ($this->_defaultGroup != $customer->getGroupId()) {
                                $customer->setGroupId($this->_defaultGroup);
                            }
                            try {
                                $customer->save();
                            } catch (Exception $e) {
                                Mage::Log($e, 'ERROR');
                                $retValue = 1;
                            }
                        }
                        Mage::getSingleton('customer/session')->loginById($customer->getId());                        
                    } else {
                        $newCustomerId = $this->_createNewUser($fbConnect->getUserBasicInfo());
                        if ($newCustomerId > 0) {
                            $session = Mage::getSingleton('customer/session');
                            $newCustomer = Mage::getModel("customer/customer")->Load($newCustomerId);
                            Mage::dispatchEvent('customer_register_success',
                                array('account_controller' => $this, 'customer' => $newCustomer)
                            );

                            if ($newCustomer->isConfirmationRequired()) {
                                $newCustomer->sendNewAccountEmail(
                                    'confirmation',
                                    $session->getBeforeAuthUrl(),
                                    Mage::app()->getStore()->getId()
                                );
                                $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));                                
                            } else {
                                $session->setCustomerAsLoggedIn($customer);                              
                                $newCustomer->sendNewAccountEmail(
                                    'registered',
                                    '',
                                    Mage::app()->getStore()->getId()
                                );                                
                            }
                            Mage::getSingleton('customer/session')->loginById($newCustomerId);
                        } else {
                            $retValue = 1;
                        }
                    }
                } else {
                    $retValue =3;
                }
            } else {
                $retValue =2;
            }                        
        }
        return $retValue;        
    }
}
