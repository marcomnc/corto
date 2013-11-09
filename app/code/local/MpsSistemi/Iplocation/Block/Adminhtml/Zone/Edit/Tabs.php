<?php

class MpsSistemi_Iplocation_Block_Adminhtml_Zone_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('mpslocation_zone_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mpslocation')->__('Informazioni sulla Zona'));
    }

    protected function _beforeToHtml()
    {
        $model = Mage::registry('mpslocation_zone');

        $this->addTab('main', array(
            'label'     => Mage::helper('mpslocation')->__('Configurazione Zona'),
            'title'     => Mage::helper('mpslocation')->__('Configurazione Zona'),
            'content'   => $this->getLayout()->createBlock('mpslocation/adminhtml_zone_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('labels', array(
            'label'     => Mage::helper('mpslocation')->__('Etichette'),
            'title'     => Mage::helper('mpslocation')->__('Etichette'),
            'content'   => $this->getLayout()->createBlock('mpslocation/adminhtml_zone_edit_tab_labels')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
    
}
