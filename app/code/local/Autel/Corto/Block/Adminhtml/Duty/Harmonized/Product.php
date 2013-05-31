<?php

class Autel_Corto_Block_Adminhtml_Duty_Harmonized_Product extends Mage_Adminhtml_Block_Widget_Container {

    public function __construct() {
       $this->_blockGroup = 'autelcorto';
       $this->_controller = 'adminhtml_duty_harmonized';
       $this->_headerText = Mage::helper('autelcorto')->__('Get Harmonized Code');
       
       parent::__construct();
       
       $this->setTemplate('autel/corto/harmonized/harmonized.phtml');
       
       
    }
    
    protected function _prepareLayout() {
        
        
        $this->setChild('grid', $this->getLayout()->createBlock('autelcorto/adminhtml_duty_harmonized_grid', 'grid'));

        parent::_prepareLayout();
        
    }
        
}

?>
