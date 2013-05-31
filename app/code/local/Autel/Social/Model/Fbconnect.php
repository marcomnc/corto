<?php

/**
 * Base Facebook Connect Class
 * 
 * @category   Autel
 * @package    Autel_Social
 * @author     Marco Mancinelli 03-06-11
 */

//Import della libreria facebook!!!!!
require_once BP . DS . 'lib'. DS . 'facebook-php-sdk' . DS . 'src' . DS . 'facebook.php';


class Autel_Social_Model_Fbconnect extends Mage_Core_Model_Abstract {
    
    protected $_facebook;
    protected $_userProfile;


    public function __construct() {
        $this->_facebook = new Facebook(array(
                          'appId'  => Mage::helper("autelsocial/fb")->getAppId(),
                          'secret' => Mage::helper("autelsocial/fb")->getSecretKey(),
                          'cookie' => true
                        ));
        $user = $this->_facebook->getUser();
        if ($user) {
            try {
                $this->_userProfile = $this->_facebook->api('/me');
            } catch (FacebookApiException $e) {
                Mage::log($e, 'ERROR');
                $this->_userProfile = null;
            }
        }
        parent::__construct();
    }
    
    public function isFbConnect() {
        return (!is_null ($this->_userProfile))?true:false;
    }
    
    public function getFbMail() {
        return (array_key_exists('email', $this->_userProfile))?$this->_userProfile['email']:null;
    }
    
    public function getFbUserProfileInfo ($infoKey) {
        return (array_key_exists($infoKey, $this->_userProfile))?$this->_userProfile[$infoKey]:null;
    }


    public function getUserBasicInfo(){
        $arrUserInfo = array();
        $arrUserInfo['first_name'] = $this->_userProfile['first_name'];
        $arrUserInfo['last_name'] = $this->_userProfile['last_name'];
        $arrUserInfo['birthday'] = $this->_userProfile['birthday'];
        $arrUserInfo['gender'] = $this->_userProfile['gender'];
        $arrUserInfo['email'] = $this->_userProfile['email'];
        $arrUserInfo['uid'] = $this->_userProfile['id'];
        return $arrUserInfo;
    }
}