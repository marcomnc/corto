<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php

class Autel_Corto_Block_Adminhtml_Zone_Group_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('autelcorto_zone_group');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('entity_');

        $fieldset = $form->addFieldset('base_fieldset', 
                array('legend'=>Mage::helper('autelcorto')->__('Gestione Gruppo Zona')));

        if ($model->getId()) {
            $fieldset->addField('entity_id', 
                                'hidden', array('name' => 'entity_id',));
        }

        $fieldset->addField('group_name', 'text', array(
                            'name'      => 'group_name',
                            'label'     => Mage::helper('autelcorto')->__('Nome Gruppo'),
                            'title'     => Mage::helper('autelcorto')->__('Nome Gruppo'),
                            'required'  => true,
                            'disabled'  => $isElementDisabled
                        ));

        $fieldset->addField('sort_order', 'text', array(
                            'name'      => 'sort_order',
                            'label'     => Mage::helper('autelcorto')->__('Order'),
                            'title'     => Mage::helper('autelcorto')->__('Order'),
                            'required'  => true,                            
                            'disabled'  => $isElementDisabled
                        ));

        Mage::dispatchEvent('autelcorto_adminhtml_zone_group_edit_form', array('form' => $form));
        
        $form->setValues($model->getData());
        
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('POST');
        $form->setAction($this->getUrl('*/*/save'));
        $this->setForm($form);

        return parent::_prepareForm();
    }

    
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        //return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
        return true;
    }
}
