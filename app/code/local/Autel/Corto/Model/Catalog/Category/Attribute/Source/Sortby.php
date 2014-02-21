<?php
/**
 * Custom Corto Moltedo
 *
 * Manage the trigger of product add to cart
 *
 * @category   Mage
 * @package    Autel_Corto
 * @author     Marco Mancinelli 
 */

class Autel_Corto_Model_Catalog_Category_Attribute_Source_Sortby extends Mage_Catalog_Model_Category_Attribute_Source_Sortby {
    
    
    /**
     * Centralizzato su Autel_Corto_Model_Catalog_Config
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            foreach ($this->_getCatalogConfig()->getAttributeUsedForSortByArray() as $k => $v) {
                $this->_options[] = array(
                    'label' => $v,
                    'value' => $k
                );
            }
        }
        return $this->_options;
    }
}
