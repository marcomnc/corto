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

class Autel_Corto_Model_Catalog_Config extends Mage_Catalog_Model_Config {
    
     const CATEGORY_SORT_TYPE = "category_sort";


    public function getAttributeUsedForSortByArray()
    {
        $options = parent::getAttributeUsedForSortByArray();
        $options[self::CATEGORY_SORT_TYPE] = Mage::Helper('autelcorto')->__("Per Categoria");
        return $options;
    }
    
}
