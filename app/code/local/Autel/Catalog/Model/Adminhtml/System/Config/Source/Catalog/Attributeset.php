<?php

/**
 * Catalog product attribute set api V2
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Model_Adminhtml_System_Config_Source_Catalog_Attributeset  {
    
    public function toOptionArray() {
        //Lista attributi
        $ret = array();
        $attrSetColl = Mage::getResourceModel('eav/entity_attribute_set_collection') 
                        ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId());
        foreach ($attrSetColl as $attrSet) {
            $ret[] = array('value'=>$attrSet->getAttributeSetId(), 'label'=>$attrSet->getAttributeSetName());
        }
        return $ret;
    }
    
}

?>
