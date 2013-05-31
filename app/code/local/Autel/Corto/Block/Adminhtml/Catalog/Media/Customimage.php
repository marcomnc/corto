<?php

/**
 * Lista dei caratteri da sostiuire nel nome delle immagini
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Corto_Block_Adminhtml_Catalog_Media_Customimage extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {     
        $this->addColumn('link', array(
            'label' => Mage::helper('autelcorto')->__('Link'),
            'style' => 'width:200px',            
        ));
        
        $this->addColumn('image', array(
            'label' => Mage::helper('autelcorto')->__('Image'),
            'style' => 'width:200px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        parent::__construct();
    }
    
}
?>
