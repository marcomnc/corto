<?php
/**
 *
 * Option Array con i blocchi statici 
 *
 * @category    Custom
 * @package     Mage_Catalog
 * @author      Marco Mancinelli
 * 
 */

class Autel_Corto_Model_Adminhtml_System_Config_Source_Cms_Block
{

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
             $_blocks = Mage::getModel('cms/block')->getCollection();
             $this->_options[] = array('value' => '', 'label' => Mage::helper('autelcorto')->__('Seleziona un blocco'));
             foreach ($_blocks as $_block) {
                 $this->_options[] = array('value' => $_block->getIdentifier(), 'label' => $_block->getTitle());
             }
        }        
        return $this->_options;
    }

}
?>