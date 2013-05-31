<?php

/**
 * Catalog product attribute set api V2
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */
class Autel_Catalog_Model_Product_Attribute_Set_Api_V2 extends Autel_Catalog_Model_Product_Attribute_Set_Api
{
    /**
     * Ritorna la lista delle cardinalità in base al prodotto o al set attributi
     * @param mixed $id      Codice del prodotto o del set di attributi
     * @param string $byType Assume i seguenti valori "sku", "productid", "set", "setid" ed identifica la 
     *                       natura del parametro precedente
     */
    public function getCardinality($id, $byType = "sku"){
        $return = array();
        $helper = MAge::helper("autelcatalog/product");
        $helper->debug("id: $id -> byTYpe: $byType");
        switch (strtolower($byType)) {
            case "sku":
                $return = $helper->getCardinalityBySku($id);
                break;
            case "productid":
                $return = $helper->getCardinalityByProductId($id);
                break;
            case "set":
                $return = $helper->getCardinalityByAttrSet($id);
                break;            
            case "setid":
                $return = $helper->getCardinalityByAttrSetId($id);
                break;            
        }

/**
 * @todo Verificare bene il return value!!!!
 */        
        return json_encode($return);
    }
    
}
