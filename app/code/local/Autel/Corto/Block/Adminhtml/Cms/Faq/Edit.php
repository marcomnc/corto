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
class Autel_Corto_Block_Adminhtml_Cms_Faq_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'autelcorto';
        $this->_controller = 'adminhtml_cms_faq';        
        
        parent::__construct();
        
        $this->setData('form_action_url', $this->getUrl('*/adminhtml_faqmanager/save'));
        $this->setSaveParametersInSession(true);
        
        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('autelcorto')->__('Salva Gruppo Faq'));
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('autelcorto')->__('Cancella Gruppo'));
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
        if (Mage::registry('autelcorto_faq')->getEntityId()) {
            return Mage::helper('autelcorto')->__("Modifica Gruppo Faq '%s'", $this->htmlEscape(Mage::registry('autelcorto_faq')->getTitle()));
        }
        else {
            return Mage::helper('autelcorto')->__('Nuovo Gruppo FAQ');
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
        return true;
    }

}

?>
