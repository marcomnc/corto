<?php

class Autel_Social_Helper_Data extends Mage_Core_Helper_Data
{
    protected $_isEnable;
    
    public function __construct() {
        $this->_isEnable =  (Mage::getStoreConfig('autelsocial/settings/enabled',Mage::app()->getStore()->getId()) == '1') ? true : false;                
    }
    
     public function isEnabled() {
        return $this->_isEnable;
    }
    

}
?>
