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

}

?>
