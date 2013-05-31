<?php

/**
 * Base Facebook Helper
 * 
 * @category   Autel
 * @package    Autel_Social
 * @author     Marco Mancinelli 03-06-11
 */
class Autel_Social_Helper_Twitter extends Autel_Social_Helper_Data
{        
    protected $_socialPage;


    public function __construct() {        
        parent::__construct();        
        $this->_secretKey = Mage::getStoreConfig('autelsocial/twitter/tw_page',Mage::app()->getStore()->getId())."";
    }
    
    public function getSocialPage() {
        return $this->_socialPage;
    }
    
}
?>
