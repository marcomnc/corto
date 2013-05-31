<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Imagetypeselector
 *
 * @author marcoma
 */
class Autel_Catalog_Block_Adminhtml_Product_Media_Imagetypeselector extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {     
        $this->addColumn('img', array(
            'label' => Mage::helper('autelcatalog')->__('Image type'),
            'style' => 'width:150px',
        ));
        
        $this->addColumn('ext', array(
            'label' => Mage::helper('autelcatalog')->__('File Ext'),
            'style' => 'width:100px',            
        ));
        
        $this->addColumn('progr', array(
            'label' => Mage::helper('autelcatalog')->__('Progressivo'),
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        parent::__construct();
    }

}

?>
