<?php

class Autel_Corto_Block_Adminhtml_Cms_Faq_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    
    protected function _prepareForm() {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 
                                           'action' => $this->getData('action'), 
                                           'method' => 'post'));
        $model = Mage::registry('autelcorto_faq');
        
        $form->setHtmlIdPrefix('entity_');
        
        $fieldset = $form->addFieldset('base_fieldset', 
                array('legend'=>Mage::helper('autelcorto')->__('Gestione FAQ')));

        if ($model->getId()) {
            $fieldset->addField('entity_id', 
                                'hidden', array('name' => 'entity_id',));
        }

        $fieldset->addField('title', 'text', array(
                            'name'      => 'title',
                            'label'     => Mage::helper('autelcorto')->__('Titolo Gruppo FAQ'),
                            'title'     => Mage::helper('autelcorto')->__('Titolo Gruppo FAQ'),
                            'required'  => true,
                            'disabled'  => false
                        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => false
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getStoreId()
            ));
            $model->setWebsiteId(Mage::app()->getStore(true)->getStoreId());
        }

        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('autelcorto')->__('Ordinamento'),
            'title'     => Mage::helper('autelcorto')->__('Ordinamento in visualizzazione'),
            'name'      => 'sort_order',
            'required'  => true,            
            'disabled'  => false,
            'class'     => 'validate-number'
        ));
        
        $faq = $fieldset->addField('faq_serial', 'text', array(
            'name'                  => 'faq_serial',
            'label'                 => 'Lista delle FAQ',                       
        ));
        
        $faq->setRenderer(Mage::getBlockSingleton('autelcorto/adminhtml_cms_faq_field_grid'));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

?>