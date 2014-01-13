<?php
/**
 * Base Social Plugin 
 * @category   Autel
 * @package    Autel_autelsocial
 * @author      MM 16-08-11
 */
class Autel_Social_Block_Fbconnect extends Autel_Social_Block_Fbabstract {
    
    protected $_fbSettings = array();
    protected $_referrer = true;


    public function __construct() {        
        $this->_fbSettings['appId'] = Mage::helper("autelsocial/fb")->getAppId();
        $this->_fbSettings['secretKey'] = Mage::helper("autelsocial/fb")->getSecretKey();
        parent::__construct(); 
    }
    
    public function getFBSettings() {
        return $this->_fbSettings;        
    }
    
    public function getFBSetting($key) {    
        return (array_key_exists($key,$this->_fbSettings))?$this->_fbSettings[$key]:null;        
    }
    
    public function getFBConnectUrl () {
        return Mage::getUrl('autelsocial/fbconnect') . '?url='. Mage::helper('core/url')->getCurrentUrl();        
    }

    public function getFBAjaxConnectUrl () {
	if ($this->getReferreCustom()!= "") {
	    return Mage::getUrl('autelsocial/fbconnect') . 'ajaxindex?url='.$this->getReferreCustom();
	}
        $_url = Mage::getUrl('autelsocial/fbconnect') . 'ajaxindex?url='. Mage::helper('core/url')->urlEncode(Mage::helper('core/url')->getCurrentUrl());
        if ($this->_referrer) {
            $_url = Mage::getUrl('autelsocial/fbconnect') . 'ajaxindex?url='. Mage::helper('core')->urlEncode($_SERVER['HTTP_REFERER']);
        }
        return $_url;
     }
    public function getImageLoading () {
        return $this->getSkinUrl('images/social/fb_loader.gif');
    }
    
    public function setRefferType($enable) {
        $this->_referrer = $enable;
    }    
}
?>
