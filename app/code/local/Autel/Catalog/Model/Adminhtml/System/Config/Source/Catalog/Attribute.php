<?php

/**
 * Catalog product attribute set api V2
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Model_Adminhtml_System_Config_Source_Catalog_Attribute  {
    
    public function toOptionArray() {
        //Lista attributi
        $ret = array();
        $attrColl = Mage::getResourceModel('eav/entity_attribute_collection') //Recuper l'id dell'attributo
                        ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId());
        foreach ($attrColl as $attr) {
            $ret[] = array('value'=>$attr->getAttributeCode(), 'label'=>$attr->getAttributeCode());
        }
        return $ret;
    }
    
}

?>
