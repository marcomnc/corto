<?php

/**
 * Lista delle sottocartelle da cui recuperare le immagini per generare gli outift
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Corto_Block_Adminhtml_Catalog_Media_Outfitfolder extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {     
        $this->addColumn('folder', array(
            'label' => Mage::helper('autelcorto')->__('Folder'),
            'style' => 'width:400px',            
        ));
        
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        parent::__construct();
    }
    
}
?>
