<?php

class Autel_Corto_Block_Adminhtml_Zone_Group_Group extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
       $this->_blockGroup = 'autelcorto';
       $this->_controller = 'adminhtml_zone_group';
       $this->_headerText = Mage::helper('autelcorto')->__('Gestione Gruppi zone di destinazione');

       parent::__construct();
    }
}

?>
