<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php

class MpsSistemi_Iplocation_Block_Adminhtml_Zone_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('mpslocation_zone');

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
                array('legend'=>Mage::helper('mpslocation')->__('Gestione Zona')));

        $fieldset = Mage::helper('mpscore/adminhtml_data_form_element')->addMiltiselect($fieldset);
        
        if ($model->getId()) {
            $fieldset->addField('entity_id', 
                                'hidden', array('name' => 'entity_id',));
        }
        
        // Per ricalcolo automatico
        $fieldset->addField('refresh_config', 
                                'hidden', array('name' => 'refresh_config', value => 0));

        $fieldset->addField('group_id', 'select', array(
                'name'      => 'group_id',
                'label'     => Mage::helper('mpslocation')->__('Group'),
                'title'     => Mage::helper('mpslocation')->__('Group'),
                'required'  => true,
                'values'    => Mage::getModel('mpslocation/zonegroup')->getCollection()->toOptionArray(),
                'disabled'  => $isElementDisabled
            ));
        
        $fieldset->addField('zone_code', 'text', array(
                            'name'      => 'zone_code',
                            'label'     => Mage::helper('mpslocation')->__('Codice Zona'),
                            'title'     => Mage::helper('mpslocation')->__('Codice Zona'),
                            'required'  => true,
                            'note'      => Mage::helper('mpslocation')->__('Mnemonic Zone Description'),
                            'disabled'  => $isElementDisabled
                        ));

        $fieldset->addField('description', 'text', array(
                            'name'      => 'description',
                            'label'     => Mage::helper('mpslocation')->__('Descrizione Zona'),
                            'title'     => Mage::helper('mpslocation')->__('Descrizione Zona'),
                            'required'  => true,                            
                            'disabled'  => $isElementDisabled
                        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_id', 'select', array(
                'name'      => 'website_id[]',
                'label'     => Mage::helper('mpslocation')->__('Attivo nel Website'),
                'title'     => Mage::helper('mpslocation')->__('Attivo nel Website'),
                'required'  => true,
                'values'    => Mage::getModel('mpscore/adminhtml_system_website')->toOptionArray(),
                'disabled'  => $isElementDisabled
            ));
            
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'store_id[]',
                'label'     => Mage::helper('mpslocation')->__('Lingue disponibili'),
                'title'     => Mage::helper('mpslocation')->__('Lingue disponibili'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled,
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
            
        }
        else {
            $fieldset->addField('website_id', 'hidden', array(
                'name'      => 'website_id[]',
                'value'     => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $model->setWebsiteId(Mage::app()->getStore(true)->getWebsiteId());
            $storeId = "";
            foreach (Mage::app()->getStores() as $store) {
                if ($store->getWebsiteId() == Mage::app()->getStore(true)->getWebsiteId()) {
                    $storeId .= (($storeId == "") ? "" : "," ) . $storeId;
                }
            }
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id[]',
                'value'     => $storeId,
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getStoreId());
        }

        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('mpslocation')->__('Ordinamento'),
            'title'     => Mage::helper('mpslocation')->__('Ordinamento in visualizzazione'),
            'name'      => 'sort_order',
            'required'  => true,            
            'disabled'  => $isElementDisabled,
        ));
        
        $fieldset->addField('state_list', 'custommultiselect', array(
            'name'      => 'state_list',
            'label'     => Mage::helper('mpslocation')->__('Lista Stati'),
            'title'     => Mage::helper('mpslocation')->__('Lista Stati della zona'),
            'required'  => true,                            
            'values'    => Mage::getSingleton('Adminhtml/System_Config_Source_Country_full')->toOptionArray(),
            'disabled'  => $isElementDisabled
        ));
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('mpslocation')->__('Main');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('mpslocation')->__('Main');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
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
