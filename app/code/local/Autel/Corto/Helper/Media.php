<?php

/**
 * Questo helper custom per Corto gestisce tute le funzoin necessarie per òla creazione dellapagina di view dinamica
 */

class Autel_Corto_Helper_Media extends Autel_Corto_Helper_Data {

    protected $_defaultPath;
    protected $_attributePath = "";

    public function __construct() {
        $this->_defaultPath = 'randomImage';
    }


    private function _readFullImage($dir) {
        $imgList = array();
        $handle = opendir($dir);
        while ($entry = readdir($handle)) {
            if ($entry != '.' &&  $entry != '..')
                $imgList[] = $entry;
        }
        closedir($handle);       
        shuffle($imgList);
        return $imgList;
    }
    
    private function _readImage($dir, $filename) {
        
        $fqn = $filename;
        if ($fqn."" != "") {
            $fqn = Mage::App()->getStore()->GetCode() . DIRECTORY_SEPARATOR . $filename;
            if (!file_exists($dir.DIRECTORY_SEPARATOR.$fqn)) {
                $fqn = $filename;
                if (!file_exists($dir.DIRECTORY_SEPARATOR.$fqn)) {
                    $fqn = "";
                }
            }
        }
        return $fqn;
    }
    
    public function getDir($type, $web = false, $dir = null) {
        if (is_null($dir)) {
            $dir = $this->_defaultPath;                    
        }
        if ($web) {
            $ret = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . str_replace(DIRECTORY_SEPARATOR, '/', $dir) . '/' . strtolower($this->_attributePath . $type);
        } else {
            $ret = Mage::getBaseDir('media'). DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . strtolower($this->_attributePath . $type);
        }
        return $ret;                
    }
    
    /**
     * Data una cartella recupera l'elenco delle immagini presenti:
     * es. Mage::Helper("autelcorto/media")->getListImage("layout/tooltip")
     * 
     * @param type $dir 
     */
    public function getListImage($dir) {       
        $imgList = $this->_readFullImage($this->getDir("",false,$dir));
        $retImg = Array();
        foreach ($imgList as $img) {
            $retImg[] = $this->getDir("",true, $dir) . $img;
        }
        return $retImg;
    }

    /**
     * Recupera la lista delle immagini / link
     * @param string $dir directory della cartella media se diversa dal default
     * @return array List casuale delle immagini
     */
    public function getListCatalogImage($dir = null) {          
        $ret =array();        
        $imageConf = unserialize(Mage::getStoreConfig("autelcorto/autelcatalog/custom_image"));
        foreach ($imageConf as $_img) {
            $_href="#";
            $_popUp=false;
            $_title="";
            if (!is_null($_img["link"])&&$_img["link"]!="") {
                if (strtolower($_img["link"])=="#contacts") {
                    $_href=MAge::getBaseUrl()."contacts/";
                    $_popUp=true;
                    $_title=$this->__('Contact Us');
                } elseif (strtolower($_img["link"])=="#news" && !MAge::getSingleton("customer/session")->isLoggedIn()) {
                    $_href=Mage::getBaseUrl()."mini-login//";
                    $_popUp=true;
                    $_title=$this->__("Login");
                } elseif (strtolower($_img["link"])=="#news" && MAge::getSingleton("customer/session")->isLoggedIn()) {
                    $_href=Mage::getBaseUrl()."subscribe-newsletter/";
                    $_popUp=true;
                    $_title=$this->__("Subscribe Newsletter");
                }
            }
            $ret[]= array("href" => $_href,
                          "src"  => $this->_readImage($this->getDir('catalog',false,$dir),$_img['image'].""),
                          "pop" => $_popUp,
                          "title" => $_title);
        }
        return  $ret;
    }   
    
    /**
     * Recupera sotto forma di JSON le l'elenco delle immagini necessarie per il fit della homepage
     */
    public function getOutfitJSON() {

        $_ret = array();
        $_folder = Mage::getStoreConfig("autelcorto/autelcatalog/outfit_folder");
        $_subFolder = unserialize(Mage::getStoreConfig("autelcorto/autelcatalog/outfit_sub_folder"));
        foreach ($_subFolder as $_sub) {          
            $imgList = $this->_readFullImage($this->getDir("",false,$_folder.$_sub['folder']));
            $retImg = Array();
			$ii = 0;
            foreach ($imgList as $img) {
                $arr = Array();
                $arr['src'] = $this->getDir("",true, $_folder.$_sub['folder']) . $img;
                $imgInfo = Mage::Helper('autelcatalog/product_attribute_media')->getSkuFromImageName($img);
				$url = "";

                if (isset($imgInfo[0])) {
                    $id = Mage::getModel('catalog/product')->getIdBySku($imgInfo[0]);
                    if (($id+0) != 0) {		
                        $prod = Mage::getModel('catalog/product')->Load($id);
                        if (!is_null($prod) && $prod->isSaleable()) {
                            $url = Mage::helper('catalog/product')->getProductUrl($prod);
                        }
                    }
                }

                $arr['url'] = $url;

                $retImg[] = $arr;
            }

            $_ret[] = $retImg;
        }

        return Mage::Helper("core")->jsonEncode($_ret);
    }
    
    public function GetAttributeArray($type) {
        if (Mage::getStoreConfig("autelcorto/autelcatalog/attribute_$type") != "") {
            return preg_split('/,/', Mage::getStoreConfig("autelcorto/autelcatalog/attribute_$type"));
        } else {
           return false;
        }
    }
    
    /**
     * Ritorna la lista delle opzioni di un attriubuto
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return type
     */
    public function getAttributeOptions($attribute, $storeId = null) {
        
        $ret = array();
        if ($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            if (!is_null($storeId)) {
                $attribute->setStoreId($storeId);
            }
            $ret = $attribute->getSource()->getAllOptions(false);
        }
        return $ret;
    }
    

    public function getBackgroudStyle($block) {

        $style= "";
        if ($block instanceof Mage_Page_Block_Html && $block->getData('show_background')) {
            $folder = "layout/sfondi/catalog";
            if ($block->hasData('custom_background_folder')) {
                $folder = $block->getData('custom_background_folder');
            }
            $imgList = Mage::Helper("autelcorto/media")->getListImage($folder);
            if (is_array($imgList) && sizeof($imgList)) {
                shuffle($imgList);
                $style  = "background-size: cover;background-position:center;";
                if(preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT'])) {
                    $style .= "filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$imgList[0]',sizingMethod='scale')";
                } else {
                    $style .= "background-image: url('$imgList[0]')";                
                }
            }
        }
        
        return $style;    
     }
    
        
}
?>
