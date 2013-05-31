<?php

/**
 * Controller utilizzato per testare le API Autel
 */
class Autel_Catalog_Testv2Controller extends Mage_Core_Controller_Front_Action {
    
    public function addoptionAction(){
        $attributeId = 85; //Colore
        $optionValue = 'xxxx';
        $valueTranslation = "Primo coes";
        $storeId=0;
        echo "<pre>";
        try {
        $aaa = Mage::getModel('catalog/product_attribute_api')->addOption($attributeId,$optionValue);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
        echo "<br>_____________________________________________________<br>";
        try {
            $aaa = Mage::getModel('catalog/product_attribute_api')
                        ->addOptionValue($attributeId ,$optionValue, $valueTranslation, 1);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
        echo "Finitooooo";
        die('finito');
    }
    
    public function testAction() {        
        $api = MAge::getModel("sales/order_api_v2");
        echo "<pre>";
        print_r ($api->info("100000215"));
        die();
        
    }
    
    public function testAZAction() {
        echo "<pre>";
        echo "pronti...<br>";
        ini_set('display_errors', 1);
        try {
            Mage::Helper("autelcatalog/product_attribute_media")->processImage("B026-2007_001.png");
            Mage::Helper("autelcatalog/product_attribute_media")->processImage("B026-2007_001.jpg");
            Mage::Helper("autelcatalog/product_attribute_media")->processImage("B026-2007_002.jpg");
            Mage::Helper("autelcatalog/product_attribute_media")->processImage("B026-2007_003.jpg");
            Mage::Helper("autelcatalog/product_attribute_media")->processImage("B026-2007_004.jpg");
            Mage::Helper("autelcatalog/product_attribute_media")->processImage("B026-2007_005.jpg");
            Mage::Helper("autelcatalog/product_attribute_media")->processImage("B026-2007_006.jpg");
        } catch (Exception $e) {
            debug_print_backtrace();
            print_r($e);
        }
        echo "<br>ci sono arrivato....";
        die();
    }
        
    public function quoteitemAction () {
        $_quote = Mage::getModel("sales/quote")->Load(201);
        $_items = $_quote->getAllItems();
        $_error = "";
        foreach ($_items as $_item) {
            try {
                $_sku = $_item->getSku();
                $_qty = $_item->getQty();
                $_size ="U";
                //if (!$this->_isProductAvailable($_sku, $_qty, $_size)) {
                    $_error = "$_sku is not available\n";
                //}
            } catch (Exception $e) {
                $_error = "General Error for $_sku\n";
            }
        }
        if ($_error != "") {
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $this->__('There was an error processing your order. \n $_error');
            Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));                         
            echo Mage::app()->getResponse()->getBody();
            die();
        }
        echo "ciao";
    }
    
    private function _isProductAvailable($sku,$qty, $size) {
        
        $_ret = $this->_getRequest($sku, $size, 0);
        return ($_ret->quantity >= $qty);
        
    }
    
    /**
     * Recupero i valori da BS3
     * @param type $sku
     * @param type $size
     * @param type $type 0 - Tutti  magazzini 1 - Dettaglio magazzini
     */
    private function _getRequest ($sku, $size, $type=1) {
        $ms = new SoapClient('http://192.168.0.201/MagentoServices/MoltedoService.svc?wsdl');
        $gpc = new GetProductsQuantity();
        if ($type != 0 && $type != 1)
            $type = 1;
        $gpc->setQuantityType($type);
        if (strpos($sku, "|") !== false) {
            $gpc->ProductsSizeList = $sku;
        } else {
            $gpc->setProductsSizeList($sku, $size);
        }
        
        $result = $ms->GetProductsQuantity($gpc);            
        return $result->GetProductsQuantityResult->ProductQuantity;
        
    }
    
    public function getcountrylistAction() {
  
        // nome del file che creeremo
        //$filename="country.xls"; 
        // specifichiamo il Content-Type
        //header ("Content-Type: application/vnd.ms-excel");
        // specifichiamo la risorsa
        //header ("Content-Disposition: inline; filename=$filename"); 
        echo "<pre>";
        print_r(Mage::helper('autelcorto')->getAllActiveState());
        echo "</pre>";
        
        $collection = Mage::getModel('directory/country')->getCollection();
        echo "<table>";
        echo "<tr>";
        echo "<td>Codice</td>";
        echo "<td>Descrizione</td>";
        echo "</tr>";
        foreach ($collection as $country)
        {
            $cid = $country->getId();
            $cname = $country->getName();
            echo "<tr>";
            echo "<td>$cid</td>";
            echo "<td>$cname</td>";
            echo "<td><pre>";
            print_r($country);
            echo "</pre></td>";
            echo "</tr>";    
        }
        echo "</table>";
        die();
    }
    
    public function sendAction() {
        $orders = Mage::getModel('sales/order')
                    ->getCollection()                    
                    ->addAttributeToFilter('increment_id', "100000126")
                    ->getFirstItem();
        
        Mage::getModel("autelcorto/observer")->checkoutOnepageSubmit($orders);
        
        echo "mail sent";
        die();
    }
    
    public function disableAction() {
        $coll = Mage::getModel("catalog/product")->getCollection()->AddAttributeToFilter("sku", "B026  2007");
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        foreach ($coll as $c) {

            $p = Mage::getModel("catalog/product")->Load($c->getId());

            $pen = Mage::getModel("catalog/product")->setStoreId(1)->Load($c->getId());
            if (is_null($p->getImage()) || $p->getImage() == "" || $p->getImage() == "no_selection") {
                echo "anullo (IMG) " . $p->getSku() ." Status : " . $p->getStatus() . " Status En :" . $pen->getStatus() . "<br>";
                continue;
            }
            if (is_null($p->getSmallImage()) || $p->getSmallImage() == "" || $p->getSmallImage() == "no_selection") {
                echo "anullo (SMALL) " . $p->getSku() ." Status : " . $p->getStatus() . " Status En :" . $pen->getStatus() . "<br>";
                continue;
            }
            
            $gallery = $p->getMediaGallery();
            if (isset($gallery["images"])) { 
                $attributes = $p->getTypeInstance(true)
                                   ->getSetAttributes($p);
                $myGallery = $attributes[Mage_Catalog_Model_Product_Attribute_Media_Api::ATTRIBUTE_CODE];
                
                $rm = false;
                
                foreach ($gallery["images"] as $g) {  
                    $file = Mage::getBaseDir() ."/media/catalog/product/" . $g["file"];
                    if (!file_exists ($file)) {
                        echo $c->getSku() . " - $file  <br>";
                        $myGallery->getBackEnd()->removeImage($p, $g["file"]);
                        $rm = true;
                    }

                }
                if ($rm) {
                    $p->save();
                }
            }
                                    
        }
    }
            
    public function storeAction() {
    	echo "<pre>";
    	
		print_r(Mage::Helper("autelcatalog/product")->getStoreByWebSite(array('1','2')));
		die();
    }
    
    public function updatecitesAction() {
        
        $filecontent = file_get_contents(Mage::getBaseDir('var') . DS . 'import' . DS . 'Classi_IdDuty.csv');
        echo "<pre>";
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        foreach (preg_split("/[\n]/", $filecontent) as $strRow) {
            if ($strRow != "") {
                $row = preg_split("/;/", $strRow);
                
                
                $sku = $row[0] ;
                $cites = $row[4] + 0;
                
                if ($cites > 0) {
                    
                    $id = Mage::getModel('catalog/product')->getIdBySku($sku);
                    
                    if ($id  > 0) {
                        $prod = Mage::getModel('catalog/product')->Load($id)->setStoreId(0);
                        $prod->setData('c_cites', $cites);
                        $prod->Save();
                        echo "Aggiornato .. Sku: $row[0] \t cites: $row[4]\n";
                    }
                }
                
                
                
            }
        }
        die();                
    }
            
}
?>
