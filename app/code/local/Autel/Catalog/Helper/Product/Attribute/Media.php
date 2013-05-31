<?php

/**
 * Helper per l'importazione del prodotto
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Helper_Product_Attribute_Media extends Autel_Catalog_Helper_Data 
{
    const   TYPE_THUMBNAIL      = "THUMB";
    const   TYPE_BASE_IMAGE     = "BIG";
    const   TYPE_SMALL_IMAGE    = "SMALL";
    
    private $_baseFolder = "";
    private $_imageFolder = "image";
    private $_videoFolder = "video";
    private $_processedFolder = "processed";
    private $_mediaAttribute = array();
    private $_galleryAttribute = array();
    
    private $_imgType = array(self::TYPE_THUMBNAIL      => "thumbnail" , 
                              self::TYPE_BASE_IMAGE     => "image",
                              self::TYPE_SMALL_IMAGE    => "small_image");
    
    
   public function __construct() {
        parent::__construct();
        $this->_separator = 
        $select = $this->_dbRead->select()
                    ->from(array('ca' => Mage::getSingleton('core/resource')->getTableName('catalog_eav_attribute')))
                    ->join(array('ea' => Mage::getSingleton('core/resource')->getTableName('eav_attribute')),
                                    "ea.attribute_id = ca.attribute_id")
                    ->where ("ea.frontend_input = 'media_image'")
                    ->orwhere ("ea.attribute_code = 'media_gallery'")
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns(array("attributeId" => "ea.attribute_id",
                                    "attributeCode" =>"ea.attribute_code"));
        foreach ($this->_dbRead->fetchAll($select) as $attr) {
            if ($attr['attributeCode'] == "media_gallery") {
                $this->_galleryAttribute = $attr['attributeId'];                
            }
            $this->_mediaAttribute[str_replace("_", "", $attr['attributeCode'])] = $attr['attributeId'];
        }     
        $this->chekUpladerDirectories();
   }
    
    /**
     * Recupero le credenziali per l'acesso FTP
     * @return array Array con "user", "password", "server", "port", "protocol", "folder"
     */
    public function getFTPConfiguration() {
        $ret = Array();
        $ret["user"] = Mage::getStoreConfig("autelconnector/connector_product_media/media_user");
        $ret["password"] = Mage::getStoreConfig("autelconnector/connector_product_media/media_password");
        $ret["server"] = Mage::getStoreConfig("autelconnector/connector_product_media/media_server");
        $ret["port"] = Mage::getStoreConfig("autelconnector/connector_product_media/media_port");
        $ret["protocol"] = Mage::getStoreConfig("autelconnector/connector_product_media/media_protocol");
        $ret["folder"] = Mage::getStoreConfig("autelconnector/connector_product_media/media_folder");
        $ret["sshkey"] = Mage::getStoreConfig("autelconnector/connector_product_media/media_sshkey");
        
        //Verifico l'esistenza delle cartelle
        $this->chekUpladerDirectories($ret["folder"]);
        $ret["folder"] = $this->_baseFolder;
        return $ret;
        
    }
    
    public function chekUpladerDirectories($folder=null) {
        if (is_null($folder)) {
            $folder = Mage::getStoreConfig("autelconnector/connector_product_media/media_folder");
        }
        if (substr($folder, -1) != DIRECTORY_SEPARATOR)
                $folder .=DIRECTORY_SEPARATOR;
        $dir = Mage::getBaseDir() . $folder. "Uploader";
        $this->_dirExistence($dir);
        $this->_dirExistence($dir. DIRECTORY_SEPARATOR . $this->_imageFolder);
        $this->_dirExistence($dir. DIRECTORY_SEPARATOR . $this->_videoFolder);
        $this->_dirExistence($dir. DIRECTORY_SEPARATOR . $this->_processedFolder);        
        $this->_baseFolder = $dir;
    }
   
    public function getImageFolder () {       
       return $this->_baseFolder . DIRECTORY_SEPARATOR . $this->_imageFolder . DIRECTORY_SEPARATOR;
    }
    
    public function processMedia () {       
              
       $image = $this->_getListImage();
       $imgProcessed = array();
       foreach($image as $img) {
           $sku = $this->_getSku($img);
           if ($sku !== false) {
               foreach ($this->_getProductList($sku[0]) as $prod) {
                   if ($this->_insertMediaGallery($img, $prod, $sku[1])) {
                       $imgProcessed[] = $img;
                   }
               }
           }
       }
       if (sizeof($imgProcessed) > 0) {
           //sposto le immagini processate
           $this->_archiveMedia($imgProcessed);
       }
    }
    
    /**
     * Rifatta la funzione 
     * @param type $sku 
     */
    public function getIdBySku($sku) {
    
        $_sku = $sku;
        if (is_array($sku)) {
            $_sku = $sku[0];
        } 
        return Mage::getModel('catalog/product')->getIdBySku ($_sku);
    }
    
    /**
     * Verifica l'esistenza di una cartella. Se non c'è la crea
     * @param type $dir 
     */
    private function _dirExistence($dir) {
        if (opendir($dir) === false) {
            if (!mkdir($dir))
                throw new Exception("Can not open Uploader Directory");            
        }

    }

    /** 
     * Ritorna un array contenente i file presenti nella cartella Image
     * @return type 
     */
    private function _getListImage() {
        $imgList = array();
        $handle = opendir($this->_baseFolder. DIRECTORY_SEPARATOR . $this->_imageFolder);
        while ($entry = readdir($handle)) {
            if ($entry != '.' &&  $entry != '..')
                $imgList[] = $entry;
        }
        closedir($handle);       
        return $imgList;
    }

    /**
     * Normalizza il codice sku sistiundo i caratteri previsti
     * @param type $codeori
     */
    private function _normalizeSku($codeori) {
        foreach (unserialize(Mage::getStoreConfig("autelconnector/connector_product_media/media_replacement")) as $norma) {
            $start = (!is_numeric($norma["from_char"]))?0:$norma["from_char"];
            $end = (!is_numeric($norma["to_char"]) || $norma["to_char"] == 0)?strlen($codeori):$norma["to_char"];

            $codeori = substr($codeori, 0, $start) . 
                       str_replace($norma["from"], $norma["to"], substr($codeori, $start, $end)) .
                       substr($codeori, $start+$end, strlen($codeori));
        }
        return $codeori;
    }
    
    /**
     * Recupero la tipologia di immagine dal nome
     * @param type $imgNane
     */
    private function _getTypeImmage ($ext, $pos) {
        
        $imgType = self::TYPE_THUMBNAIL;
        $ext = strtoupper($ext);
        $pos = (int)$pos;
        foreach (unserialize(Mage::getStoreConfig("autelconnector/connector_product_media/image_type_selector")) as $type) {            
            if (array_key_exists(strtoupper($type["img"]), $this->_imgType)) {
                if ($ext == strtoupper($type["ext"]) && ($pos == $type["progr"] || $type["progr"] == "*" || $type["progr"] < 0)) {
                    $imgType = $type["img"];
                    break;
                }
                if ($pos == $type["progr"] && ($ext == strtoupper($type["ext"]) || $type["ext"] == "" || $type["ext"] = "*" )) {
                    $imgType = $type["img"];
                    break;
                }
            }
        }
        return $imgType;
    }
    
    public function getSkuFromImageName($imageName) {
        return $this->_getSku($imageName);
    }
    
    /**
     * Recupera le informazioni dal nome dell'immagine 
     * @param type $imageName Nome dell'immagine
     * @return array 
     *      0 - Sku, 
     *      1 - Progressivo
     *      2 - Estensione dell'immagine
     *      3 - Tipologia di immagine 
     */
    private function _getSku($imageName) {        
        $imgSeparator = Mage::getStoreConfig("autelconnector/connector_product_media/media_separator");
        if (substr_count($imageName, $imgSeparator) > 0) {
            $pos = strpos($imageName, $imgSeparator);
            $retVal[0] = substr($imageName, 0, $pos);
            $retVal[0] = $this->_normalizeSku($retVal[0]);
            $retVal[1] = substr($imageName, $pos+1, strlen($imageName)-$pos-5);
            $retVal[2] = strtoupper(substr($imageName, -3));   
            //Verifico che tipologia di immagine è
            $retVal[3] = $this->_getTypeImmage($retVal[2], $retVal[1]);
        } else {
            return $retVal = false;
        } 
        return $retVal;        
    }
        
    private function _getProductList($sku) {
        
        $productList = array();
        $id = $this->getIdBySku($sku);
        if ($id>0) {
            $prod = Mage::getModel("catalog/product")->Load($id);
            $productList[] = $id;
            if ($prod->getTypeId() == "configurable") {
                //Recupero tutti gli articoli collegati
                foreach (Mage::getModel("catalog/product_type_configurable")->getChildrenIds($id) as $simple) {
                    $productList[] = $simple;
                }
            } 
        }
        return $productList;        
    }
    
    private function _insertMediaGallery($img, $idProd, $position = 0) {
        
        //Verifico se l'immagine esiste
        $select = $this->_dbRead->select()
                       ->from(array("gallery"=>Mage::getSingleton('core/resource')->
                                        getTableName('catalog_product_entity_media_gallery')))
                       ->joinleft(array("position"=>Mage::getSingleton('core/resource')->
                                        getTableName('catalog_product_entity_media_gallery_value')),
                                  "gallery.value_id = position.value_id")
                       ->where('gallery.product_id = ?', $idProd)
                       ->where('gallery.attribute_id = ?', $this->_galleryAttribute)
                       ->where ('position.position = ?', $position)
                       ->reset(Zend_Db_Select::COLUMNS)
                       ->columns(array("valueId" => "gallery.value_id"));

        if ($this->_dbRead->fetchOne($select)."" != "") {
            //Esiste già, cancello e reinserisco
            
        }
        
    }
    
    /**
     * Retrieve gallery attribute from product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute|boolean
     */
    protected function _getGalleryAttribute($product)
    {
        $attributes = $product->getTypeInstance(true)
            ->getSetAttributes($product);

        if (!isset($attributes[Mage_Catalog_Model_Product_Attribute_Media_Api::ATTRIBUTE_CODE])) {
            $this->_fault('not_media');
        }

        return $attributes[Mage_Catalog_Model_Product_Attribute_Media_Api::ATTRIBUTE_CODE];
    }
    
    protected function _identifyType($prod, $file) {
        if ($prod->getImage() == $file) {
            return self::TYPE_BASE_IMAGE;
        }
        if ($prod->getSmallImage() == $file) {
            return self::TYPE_SMALL_IMAGE;
        }
        if ($prod->getThumbnail() == $file) {
            return self::TYPE_THUMBNAIL;
        }
        return false;
    }


    /**
     * Processo l'immageine
     * @param type $imgName
     * @return int 0 - Tutto Ok, 1 - Error, 2 - File non valido
     */
    public function processImage ($imgName) {
        
        $retVal = 0;
        $this->debug ("Nome dell'immage che sto per processare: $imgName");
        $sku = $this->_getSku($imgName);
        $this->debug ("Informazioni Legate all'immagine");
        $this->debug ($sku);
        if (is_array($sku) !== false) {            
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            $incBig = Mage::getStoreConfig("autelconnector/connector_product_media/exclude_big_gallery");
            $_imported = false;
            foreach ($this->_getProductList($sku[0]) as $prod) {   
                $_imported = true;
                $product = Mage::getModel('catalog/product')->Load($prod);
                $gallery = $this->_getGalleryAttribute($product);   
                
                $productGallery = $product->getMediaGallery();
                if (isset($productGallery["images"])) { //Esiste una galleria
                    foreach ($productGallery["images"] as $item) {
                        $imgType = $this->_identifyType($product, $item["file"]);
                        if ($item["position"] == $sku[1] && ($imgType===false || $sku[3] == $imgType)) {
                            $this->debug("Cancello immagine già presente");
                            try {
                                $gallery->getBackEnd()->removeImage($product, $item["file"]);
                                //$product->save();   
                            } catch (Exception $e) {
                                Mage::LogException("Errore remove", Zend_Log::ERR);
                                Mage::LogEception($e->getMessage(), Zend_Log::ERR);
                                $this->debug("Errore in fase di cancellazione dell'immagine. Verifica log delle eccezioni");
                                $retVal = 1;
                            }
                        }
                    }
                }
                
// Usato l'array invece dell'oggetto items xchè altrimenti non prende quelle escluse
//                foreach ($product->getMediaGalleryImages()->getItems() as $item) {                    
//                    $imgType = $this->_identifyType($product, $item->getFile());
//                    if ($item->getPosition() == $sku[1] && ($imgType===false || $sku[3] == $imgType)) {
//                        $this->debug("Cancello immagine già presente");
//                        try {
//                            $gallery->getBackEnd()->removeImage($product, $item->getFile());
//                        } catch (Exception $e) {
//                            Mage::LogException("Errore remove", Zend_Log::ERR);
//                            Mage::LogEception($e->getMessage(), Zend_Log::ERR);
//                            $this->debug("Errore in fase di cancellazione dell'immagine. Verifica log delle eccezioni");
//                            $retVal = 1;
//                        }
//                    }
//                }

                $this->debug("Provo a caricare l'immagine " .$this->_baseFolder .DIRECTORY_SEPARATOR . $this->_imageFolder . DIRECTORY_SEPARATOR . $imgName ." per " . $product->getSku());                
                // Adding image to gallery
//                public function addImage(Mage_Catalog_Model_Product $product, $file,
//                        $mediaAttribute = null, $move = false, $exclude = true)                
                $file = $gallery->getBackend()->addImage(
                    $product,
                    $this->_baseFolder .DIRECTORY_SEPARATOR . $this->_imageFolder . DIRECTORY_SEPARATOR . $imgName,
                    null,
                    false, 
                    ($sku[3] == self::TYPE_BASE_IMAGE && $incBig)
                );
                $this->debug("Caricata l'immagine in $file" );
                
                $this->debug("Assegno posizione " . $sku[1]);
                $gallery->getBackEnd()->updateImage($product, $file, array("position" => $sku[1]));
                
                $this->debug("Setto la tipoliga dell'immagine " .$this->_imgType[$sku[3]] ." -> " . $sku[3]);
                $product->setData($this->_imgType[$sku[3]],$file);
                try {
                    $product->save();                
                } catch (Exception $e) {
                    Mage::LogException("Errore remove", Zend_Log::ERR);
                    Mage::LogEception($e->getMessage(), Zend_Log::ERR);
                    $this->debug("Errore in fase di memorizzazione dell'immagine. Verifica log delle eccezioni");
                    $retVal = 1;
                }
            }
            if (!$_imported) {
                $this->debug("Nessun prodotto associato all'imagine $imgName");
                $retVal = 2;
            }
        } else {
            $this->debug("Nome immagine strutturato male " . $imgName);
            $retVal = 2;
        }
        $this->archiveMedia($imgName);
               
        return $retVal;
    }
    
    /**
     * Archiviazione delle immagini in un file ZIP
     * @param string|array $imageList
     * @param bool $remove Indica se cancellare o meno le immgini zippate
     */
    public function archiveMedia($imageList, $remove = true) {
        $_elab =array();
        if (!is_array($imageList)) {
            $_elab[] = $imageList;
        } else {
            $_elab = $imageList;
        }
        $_archiveName = $this->_baseFolder . DIRECTORY_SEPARATOR . $this->_processedFolder . DIRECTORY_SEPARATOR;
        $_archiveName .= strtoupper(Mage::getSingleton("api/session")->getSessionId()) .'-'. date("Ymd") .".zip";
        $_zip = new ZipArchive;
        $_zip->open($_archiveName, ZipArchive::CREATE);
        foreach ($_elab as $_img) {
            try {
                $_imgComp = $this->_baseFolder .DIRECTORY_SEPARATOR . $this->_imageFolder . DIRECTORY_SEPARATOR . $_img;        
                $_zip->addFile($_imgComp, $this->_imageFolder . DIRECTORY_SEPARATOR . $_img);
            } catch (Exception $e) {
                Mage::LogException("Errore Archive", Zend_Log::ERR);
                Mage::LogEception($e->getMessage(), Zend_Log::ERR);
                $this->debug("Errore in fase di Archiviazione dell'immagine. Verifica log delle eccezioni");                
            }            
        }
        $_zip->close();
        foreach ($_elab as $_img) {
            $_img = $this->_baseFolder .DIRECTORY_SEPARATOR . $this->_imageFolder . DIRECTORY_SEPARATOR . $_img;        
            unlink($_img);
        }
    }

    
}

?>
