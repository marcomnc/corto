<?php

class Autel_Corto_Block_Adminhtml_Zone_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('autelcorto_zone_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('autelcorto')->__('Informazioni sulla Zona'));
    }

    protected function _beforeToHtml()
    {
        $model = Mage::registry('autelcorto_zone');

        $this->addTab('main', array(
            'label'     => Mage::helper('autelcorto')->__('Configurazione Zona'),
            'title'     => Mage::helper('autelcorto')->__('Configurazione Zona'),
            'content'   => $this->getLayout()->createBlock('autelcorto/adminhtml_zone_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('labels', array(
            'label'     => Mage::helper('autelcorto')->__('Etichette'),
            'title'     => Mage::helper('autelcorto')->__('Etichette'),
            'content'   => $this->getLayout()->createBlock('autelcorto/adminhtml_zone_edit_tab_labels')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
    
}
