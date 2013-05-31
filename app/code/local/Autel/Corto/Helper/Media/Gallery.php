<?php

/**
 * Questo helper custom per Corto gestisce tute le funzoin necessarie per òla creazione dellapagina di view dinamica
 */

class Autel_Corto_Helper_Media_Gallery extends Autel_Corto_Helper_Media {
    
    private $_positionBack = 101;

    public function getCatalogBackImage($product, $size = 225) {
        
        $_ret = null;
        $_product = Mage::getModel("catalog/product")->Load($product->getId());
        $_mediaGallery = $_product->getMediaGalleryImages();      
        foreach ($_mediaGallery as $_image) {                
            if ($_image->getPositionDefault() == $this->_positionBack) {
                $_ret = Mage::Helper('catalog/image')
                            ->init($_product, 'thumbnail', $_image->getFile())
                            ->resize($size)->__toString(); 
            }
        }
        return $_ret;
        
    }
}
?>
