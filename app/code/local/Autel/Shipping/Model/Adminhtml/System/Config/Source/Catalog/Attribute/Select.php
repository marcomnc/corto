<?php


/**
 *
 * Option Array con gli attributi di tipo select
 *
 * @category    Custom
 * @package     Mage_Shipping
 * @author      Marco Mancinelli
 * 
 */

class Autel_Shipping_Model_Adminhtml_System_Config_Source_Catalog_Attribute_Select {
    
    public function toOptionArray() {
        $_attList =  $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                        ->addFieldToFilter('frontend_input',array("in" => array("select")))
                        ->addStoreLabel(Mage::app()->getStore()->getId())
                        ->setOrder('main_table.attribute_id', 'asc')
                        ->load();
        $_retArray = array();
        $_retArray[] = array ('value' => null, 'label' => Mage::Helper('autelshipping')->__('Seleziona Attributo'));
        foreach ($_attList as $_att) {
            $_retArray[] = array ('value' => $_att->getAttributeCode(), 
                                  'label' => $_att->getFrontendLabel());
        }
        return $_retArray;
    }
    
}

?>
