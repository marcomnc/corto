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

/**
 * Classe per passare i parametri
 */        
class GetProductsQuantity {
    /**
     * Lista codici articolo separata da pipe
     * il codice deve essere seguito da ; + taglia 
     * @var String
     */
    public $ProductsSizeList = "";
    /**
     * Tipo di elenco da ritornare effettuare:
     * 0 - Tutti i negozi
     * 1 - Dettaglio per Singolo negozio
     * @var int 
     */
    public $QuantityType = 1;
    
    public function setQuantityType($q) {
        if ($q != 0 && $q != 1) {
            $q = 0;
        }
        $this->QuantityType = $q;
    }
    
    public function setProductsSizeList($sku, $size, $add=false) {
        if ($size == "") {
            $size = "U";
        }
        $sku = "$sku;$size";
        if (!$add ||$this->ProductsSizeList=="") {
            $this->ProductsSizeList = $sku;
        } else {
            $this->ProductsSizeList .="|$sku";
        }
    }
}


class Autel_Corto_Model_Observer {
 
    /**
     * Quando aggiungo un articolo al carrello (sel la chiamata è stata fatta in ajax) 
     * ritorno il blocco del carrello come dati
     * 
     * Nell'observer ho a disposizione il product, request e reponse
     * 
     * utilizzo solo il requeste per leggere il parametro ajax. 
     * ho già salvato il carrello per cui ritorno il blocco del side bar ed il contatore
     * 
     * Se non è ajax vado come prima
     * 
     * @param type $observer 
     */
    public function cart_add_product_complete ($observer) {        
        $request = $observer->getRequest();
        if ($request->getParam("ajax")==1) {
            $retJson = array();
            $layout = Mage::app()->getFrontController()->getAction()->loadLayout()->getLayout();            
            $retJson["status"]= "OK";
            $retJson["lightCart"] = $layout->createBlock("checkout/cart_sidebar")->
                                            setTemplate("checkout/cart/light.phtml")
                                            ->toHtml();
           
            try {
                $retJson["topLink"] = $layout->getBlock('top.links')->toHtml();
            } catch (Exception $e) {
                
            }            
            Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($retJson));                         
            echo Mage::app()->getResponse()->getBody();
            die();
        } else  {
            Mage::getSingleton("customer/session")->setAddProductToCart(true);   
        }   
    }
    
    /**
     * Verifico se la merce è realmente in giacenza
     * @param type $observer
     */
    public function checkout_onepage_save_order ($observer) {
	
        $_quote = $observer->getQuote();
        $_items = $_quote->getAllItems();
        $_error = "";
        foreach ($_items as $_item) {        	        	
            try {
                $_sku = $_item->getSku();
                $_qty = $_item->getQty();
                $_size ="U";
                if (!$this->_isProductAvailable($_sku, $_qty, $_size)) {
                    $_error = "$_sku is not available\n";
                }
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
        return ($observer);
    }
    
    /**
     * Invio la mail al primo deposito che ha in giacenza la merce
     * @param type $observer
     */
    public function checkoutOnepageSubmit ($order) {
        $myOrder = Mage::getModel('sales/order')->Load($order->getId());
        $_items = $myOrder->getItemsCollection();
              
        foreach ($_items as $_item) {

            $_qty = $_item->getQtyOrdered() + 0;
            $_disp = $this->_getRequest($_item->getSku(), "U", 1);
            foreach ($_disp as $_d) {
                
                if ($_qty <= $_d->quantity) {
                    $text = "Riservare N. " . $_qty . " Borse "  . $_item->getSku() . " \n";
                    $text .= "per l'ordine " . $myOrder->getIncrementId() . " intestato a " . $myOrder->getCustomerName();
                    $mail = $_d->depp_mail;
                    if (trim($mail) != "") {
                        if (!mail($mail,'Piking',$text,'From: no-reply@corto.com')) {
                            Mage::LogException("Errore durante l'invio della mail di piking per l'ordine al negozio" . $order->getIncrmentId());
                        }
                    }
                    $text .= "\n Negozio a cui è stata fatta la notifica : " . $_d->depp_desc;
                    if (!mail('sales@corto.com','Piking',$text,'From: no-reply@corto.com')) {
                        Mage::LogException("Errore durante l'invio della mail di piking per l'ordine a sales" . $order->getIncrmentId());
                    }

                    break;
                }                                                   
            }            
        }
    }
    
    public function isProductAvailable($sku,$qty, $size) {
        
        $_ret = $this->_getRequest($sku, $size, 0);
        $_myQty = $_ret->quantity;        
        return ($_myQty >= $qty);
        
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
    
    private function _sendMail($mail,$sku, $size, $qty, $id) {
        // Invio la mail
        $mailTemplate = Mage::getModel('core/email_template');

        $post['sku']  = $sku;
        $pro = Mage::getModel("catalog/product")->Load($id);
        $post['name'] = $pro->getName();
        if ($size != "" && $size != "U") 
            $post['size'] = $size;
        $post['qty']    = $qty;
        $postObject = new Varien_Object();
        $postObject->setData($post);
        
        if ($mail == "") {
            $mail = "marco@mancinellimarco.it";
        }
        $text = "Pealse Reserve for the order ";
//Mage::Log("Cerco di mandare la mail");
//Mage::Log($post);
//        try {
//            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
//                ->setReplyTo("noreply@corto.com")
//                ->sendTransactional(
//                    Mage::getStoreConfig('autelcorto/emailavailability/warehouse_email_template'),
//                    null,
//                    $mail,
//                    null,
//                    array('data' => $postObject));
//
//            if (!$mailTemplate->getSentSuccess()) {
//                $retArray[0] = 'ko';
//                $retArray[0] = 'email send failed';
//            } else {
//                $retArray[0] = 'ok';
//            }
//Mage::log($retArray);            
//        } catch (Exception $e) {
//            Mage::Log($e);
//        }
        
    }
    
    /**
     * Se la valuta e JPY Riassegno la formattazione
     * @param type $observer
     * @return type
     */
    public function format_currency ($observer) {

        $currencyFromat = unserialize(Mage::getStoreConfig('autelcorto/autelcatalog/custom_currency_format'));
        
        $options = $observer->getData('currency_options');
        $currency = $observer->getData('base_code');

        $newFormat = '';
        if (is_array($currencyFromat)) {
            foreach ($currencyFromat as $cFormat) {
                if ($cFormat['Currency'] == $currency) {
                    $newFormat = $cFormat['Format'];
                    break;
                }
            }
        }
        
        if ($newFormat ==  '') {
            
            $newFA = preg_split('//', Zend_Locale_Data::getContent(Mage::app()->getLocale()->getLocaleCode(), 'currencynumber', 'default'), -1, PREG_SPLIT_NO_EMPTY);
                
            if (ord($newFA[0]) == 194 && ord($newFA[1]) == 164 && $newFA[2] != ' ') {
                $newFAA = array();
                for ($i = 0; $i < sizeof($newFA); $i++) {
                    if ($i > 0 && ord($newFA[$i-1]) == 164) {
                        $newFAA[] = ' ';
                    }
                    $newFAA[] = $newFA[$i];
                }
                $newFormat = implode("", $newFAA);
            }
        }
        
        $options['format'] = $newFormat;
        
        $observer->setData('currency_options', $options);
        return $observer;


    }
    
    public function cms_page_render($observer) {
    
        $action = $observer->getControllerAction();
        $page = $observer->getPage();
       
        
        $getParams = $action->getRequest()->getParams();
        $postParams = $action->getRequest()->getPost();
        
        $isajax = (isset($getParams['is_ajax']) || isset($postParams['is_ajax'])) ? true : false;
        
        if ($isajax) {
            $page->unsCustomLayoutUpdateXml();
            $page->unsLayoutUpdateXml();


            if(($customLayout = Mage::getStoreConfig('autelcorto/autelpage/custom_layout_ajax_page')) != '') {
                $page->setLayoutUpdateXml($customLayout);
            }
        }
        
        $observer->setPage($page);
        return ($observer);
    }
}
?>
