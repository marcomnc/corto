<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Edit
 *
 * @author marcoma
 */
class Autel_Corto_Block_Adminhtml_Zone_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'autelcorto';
        $this->_controller = 'adminhtml_zone';        
        parent::__construct();
        $this->setSaveParametersInSession(true);

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('autelcorto')->__('Salva Zona'));

            if (Mage::registry('autelcorto_zone')->getEntityId()) {
                $this->_addButton('refresh', array(
                                'label'     => Mage::helper('autelcorto')->__('Refresh Config'),
                                'onclick'   => 'setLocation(\'' . $this->_getRefreshUrl() . '\')',
                                'class'     => 'save',
                               ), 2);
            }
            $this->_addButton('save_refresh', array(
                              'label'     => Mage::helper('autelcorto')->__('Save / Refresh'),
                              'onclick'   => "$('entity_refresh_config').value=1;editForm.submit();",
                              'class'     => 'save',
                             ), 3);
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('autelcorto')->__('Delete Zone'));
        } else {
            $this->_removeButton('delete');
        }

    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('autelcorto_zone')->getEntityId()) {
            return Mage::helper('autelcorto')->__("Modifica Zona '%s'", $this->htmlEscape(Mage::registry('autelcorto_zone')->getZoneCode()));
        }
        else {
            return Mage::helper('autelcorto')->__('Nuova Zona');
        }
    }

    /**
     * Check permission for passed action
     * @toDo
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        //return Mage::getSingleton('admin/session')->isAllowed('mps/mpspricezone/' . $action);
        return true;
    }
    
    private function _getRefreshUrl() {
        return $this->getUrl('*/*/refresh', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

}

?>
