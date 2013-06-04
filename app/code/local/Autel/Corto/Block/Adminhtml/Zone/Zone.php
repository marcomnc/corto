<?php

class Autel_Corto_Block_Adminhtml_Zone_Zone extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
       $this->_blockGroup = 'autelcorto';
       $this->_controller = 'adminhtml_zone';
       $this->_headerText = Mage::helper('autelcorto')->__('Gestione Zone Selezione Destinazione');
       
       $this->_addButton('refresh', array(
            'label'     => Mage::helper('autelcorto')->__('Refresh Config'),
            'onclick'   => "location.href='" . $this->getUrl('*/*/refresh') . "'",
            'class'     => '',
        ));

       parent::__construct();
    }
}

?>
