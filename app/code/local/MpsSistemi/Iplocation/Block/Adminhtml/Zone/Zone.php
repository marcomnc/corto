<?php

class MpsSistemi_Iplocation_Block_Adminhtml_Zone_Zone extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
       $this->_blockGroup = 'mpslocation';
       $this->_controller = 'adminhtml_zone';
       $this->_headerText = Mage::helper('mpslocation')->__('Gestione Zone Selezione Destinazione');
       
       $this->_addButton('refresh', array(
            'label'     => Mage::helper('mpslocation')->__('Refresh Config'),
            'onclick'   => "location.href='" . $this->getUrl('*/*/refresh') . "'",
            'class'     => '',
        ));

       parent::__construct();
    }
}

?>
