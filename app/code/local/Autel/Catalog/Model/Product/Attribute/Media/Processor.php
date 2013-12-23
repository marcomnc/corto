<?php
/**
 * Catalog product Processing Image
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */
class Autel_Catalog_Model_Product_Attribute_Media_Processor 
{
    
    public function Execute() {
        return MAge::Helper("autelcatalog/product_attribute_media")->processMedia();
    } 
}

?>
