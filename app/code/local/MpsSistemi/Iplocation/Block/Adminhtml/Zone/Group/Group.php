<?php

class MpsSistemi_Iplocation_Block_Adminhtml_Zone_Group_Group extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
       $this->_blockGroup = 'mpslocation';
       $this->_controller = 'adminhtml_zone_group';
       $this->_headerText = Mage::helper('mpslocation')->__('Gestione Gruppi zone di destinazione');

       parent::__construct();
    }
}

?>
