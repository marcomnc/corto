<?php

/**
 * Lista dei caratteri da sostiuire nel nome delle immagini
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Block_Adminhtml_Product_Replacemediachar extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {     
        $this->addColumn('from', array(
            'label' => Mage::helper('autelcatalog')->__('Da Car.'),
            'style' => 'width:100px',            
        ));
        
        $this->addColumn('to', array(
            'label' => Mage::helper('autelcatalog')->__('A Car'),
            'style' => 'width:100px',
        ));
        $this->addColumn('from_char', array(
            'label' => Mage::helper('autelcatalog')->__('Da Pos.'),
            'style' => 'width:100px',            
        ));
        
        $this->addColumn('to_char', array(
            'label' => Mage::helper('autelcatalog')->__('A Pos.'),
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        parent::__construct();
    }
    
}
?>
