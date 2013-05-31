<?php

class Autel_Corto_Block_Adminhtml_Cms_Faq_Faq extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
       $this->_blockGroup = 'autelcorto';
       $this->_controller = 'adminhtml_cms_faq';
       $this->_headerText = Mage::helper('autelcorto')->__('Gestione dell F.A.Q.');

       parent::__construct();
    }
}

?>
