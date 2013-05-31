<?php

/**
 * Base Social Plugin 
 * @category   Autel
 * @package    Autel_Social
 * @author     Marco Mancinelli 03-06-11
 */
class Autel_Social_Block_FBabstract extends Mage_Core_Block_Template {
    
    
    public function __construct() {
        parent::__construct(); 
    }
    
    public function isEnabled() {
        return Mage::Helper("autelsocial")->isEnabled();
    }
    
}
?>
