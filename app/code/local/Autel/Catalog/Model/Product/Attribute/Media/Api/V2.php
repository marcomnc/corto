<?php
/**
 * Catalog product attribute Media api V2
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Model_Product_Attribute_Media_Api_V2 extends Mage_Catalog_Model_Product_Attribute_Media_Api
{
    /**
     * Recuper i parmetri dell'FTP
     * @return type 
     */
    public function getFTPParams() {
        return Mage::Helper("autelcatalog/product_attribute_media")->getFTPConfiguration();
    }
    
    /**
     * Esegue la procedura che elabora le immagini caricate
     * @return int numero di immagini processate
     */
    public function processMedia() {        
        return Mage::getModel('autelcatalog/product_attribute_media_processor')->Execute();        
    }
    
        /**
     *  Carico un immagine e l'essegno ad un prodotto
     * @param string $fileName  Nome del file da cui scaturiscono le regole
     * @param string $base64    Imagine codificata
     * @return bool             Si sono verificati errori e/o non è stato possibile associare l'immagine
     */
    public function uploadImage($fileName, $base64) {
        
        $_hlp = Mage::Helper("autelcatalog/product_attribute_media");
        $_fileName = $_hlp->getImageFolder() . $fileName;
        if (file_exists($_fileName)) {
            unlink($_fileName);
        }
        
        
        $img = imagecreatefromstring(base64_decode($base64));
        if($img != false) {
        switch (strtolower(substr($fileName,-3))) {
            case "png": 
                imagealphablending($img, false);
                imagesavealpha($img, true);
                imagepng($img, $_hlp->getImageFolder() . $fileName);
                break;
            case "jpg":
                imagejpeg($img, $_hlp->getImageFolder() . $fileName);
                break;
            case "gif":
                imagegif($img, $_hlp->getImageFolder() . $fileName);
                break;

            default:
                return 2;
        }
        } else {
            return 2;
        }
        
        return $_hlp->processImage($fileName);
    }
}

?>
