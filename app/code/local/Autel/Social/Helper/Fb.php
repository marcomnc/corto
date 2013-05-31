<?php

/**
 * Base Facebook Helper
 * 
 * @category   Autel
 * @package    Autel_Social
 * @author     Marco Mancinelli 03-06-11
 */
class Autel_Social_Helper_Fb extends Autel_Social_Helper_Data
{
        
    protected $_appId;
    protected $_secretKey;
    protected $_socialPage;


    public function __construct() {        
        parent::__construct();        
        $this->_appId =  Mage::getStoreConfig('autelsocial/facebook/fb_appId',Mage::app()->getStore()->getId())."";
        $this->_secretKey = Mage::getStoreConfig('autelsocial/facebook/fb_secretKey',Mage::app()->getStore()->getId())."";
        $this->_socialPage = Mage::getStoreConfig('autelsocial/facebook/fb_page',Mage::app()->getStore()->getId())."";
    }
       
    public function getAppId() {
        return $this->_appId;        
    }
    
    public function getSecretKey() {
        return $this->_secretKey;
    }
    
    public function getSocialPage() {
        return $this->_socialPage;
    }
    
}
?>
