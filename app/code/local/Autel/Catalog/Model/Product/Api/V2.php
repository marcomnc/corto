<?php

/**
 * Catalog product attribute set api V2
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Model_Product_Api_V2 extends Mage_Catalog_Model_Product_Api_V2 
{
 
    protected $_dbRead;
    protected $_dbWrite;

    protected $_attributeRow = array();    
    protected $_cardinality = array();
    
    protected $_allUpdate = Array();
    protected $_forceUpdate = Array();
       
    protected $_hlp;
    protected $_catHlp;
    protected $_action;
    
    private $_entityTypeId = 4;
    private $_table = array();
    


    public function __construct() {
        $this->_dbRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_dbWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        $this->_allUpdate = explode(",", Mage::getStoreConfig('autelconnector/connector_product/attribute_all_update', 0)); 
        $this->_forceUpdate = explode(",", Mage::getStoreConfig('autelconnector/connector_product/attribute_force_update', 0)); 
        $this->_hlp = Mage::helper("autelcatalog/product");
        $this->_catHlp = Mage::helper("autelcatalog/product_category");
        $this->_action = Mage::getModel('catalog/product_action');        
        
        $this->_table['product']     = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity');
        $this->_table['website']     = Mage::getSingleton('core/resource')->getTableName('catalog_product_website');
        $this->_table['category']    = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
        $this->_table['stock']       = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
        $this->_table['stockstatus'] = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_status');
        
        $timoutLimit = Mage::getStoreConfig('autelconnector/connector_product/attribute_force_update', 0);
        
        if ($timoutLimit == 0) {
            ignore_user_abort();
        }
        set_time_limit(0);
    }
    
    
    public function test($input){
//        $this->_hlp->debugImport(Autel_Catalog_Helper_Data::DEBUG_ACTION_START, 10);
//        $this->_hlp->debugImport(Autel_Catalog_Helper_Data::DEBUG_ACTION_UPDATE, 1);
//        $this->_hlp->debugImport(Autel_Catalog_Helper_Data::DEBUG_ACTION_UPDATE, 1);
//        $this->_hlp->debugImport(Autel_Catalog_Helper_Data::DEBUG_ACTION_UPDATE, 1);
//        $this->_hlp->debugImport(Autel_Catalog_Helper_Data::DEBUG_ACTION_END);
        return 0;
    }
    
    public function importzip($input) {
        
        $str =  base64_decode($input);

        return $this->import (gzinflate($str));
    }
    
    
    /**
     * Importazione prodotti. 
     * API che si occupa dell'importazione dei prodotti. L'API accetta in input una Lista di oggetti
     * serializzato dalla Libreria MageService (.NET).
     * I prodotti vengono creati o aggiornati (se previsto dal falg force full update). In ogni 
     * caso viene sempre aggiornata la giacenza di magazzino. Se vengono passati i rifermenti necessari 
     * alla creazione/associazione dei prodtti configurabili gli stessi vengono creati.
     * 
     * L'attributeset viene passato come ID. Esiste una API per recuperare la lista degli attribute set
     * e degli attributi associati
     * 
     * Gli attributi vegnono passati come una lista Key/Value dove su Key c'è l'attribute_code, mentre su
     * value la label dell'attributo legata allo store o allo store di default
     * 
     * @todo per le categorie adesso viene passate come ID, sarebbe da fare in modo che sia possibile passare 
     * la descrizione (mettendo magari nel default store il codice del gestionale
     * 
     * @todo implementare la possibiltà di creare l'opzione dell'attributo se non esiste
     * 
     * @todo implementare un ritorno (JSON) con eventuali errori rilevati
     * 
     * @param JSON $input Stringa JSON con l'oggetto prodotto serializato
     * @return type 
     */
    public function import($input) {
//        $this->_hlp->debug($input);
//        $this->_hlp->debug(json_decode($input));

        $productList = json_decode($input);
        $linkArray = array();
        $cardArray = array();
        $this->_hlp->debugImport(Autel_Catalog_Helper_Data::DEBUG_ACTION_START, sizeof($productList));
        
        //Memorizzo  la modalità degli indici
        $_oriProcessor = array();
        foreach (Mage::getSingleton('index/indexer')->getProcessesCollection() as $pProcess) {
            $_oriProcessor[$pProcess->getId()] = $pProcess->getMode();
            $pProcess->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
        }       

        //Creo i prodotti                
        foreach ($productList as &$product) {            
            if ($this->_isDataComplete($product)===true) {                
                $id = $this->_fastCreateProduct($product);
                if (is_numeric($id) && $id > 0) {
                    $product->Id == $id;
                    if ($product->SkuConfigurable."" != "") {                    
                        $linkArray[$product->SkuConfigurable][] = array('sku' => $product->Sku,
                                                                        'id'  => $id);
                    } 
                    //La cardinalità la trovo solo su prodotti configurabili
                    if (isset($product->Cardinality) && $product->Type=='configurable') {
                        foreach ($product->Cardinality as $cardinal) {
                            $cardArray[$product->Sku][] = array("attributeCode" => $cardinal->Code);
                        }
                    }
                } else $product->Error = $id;
            } else {
                $product->Error = $this->_isDataComplete($product);
            }
            $this->_hlp->debugImport(Autel_Catalog_Helper_Data::DEBUG_ACTION_UPDATE);
        }
        
        $this->_hlp->debug("Fine Creazione Prodotti");
        $this->_hlp->debug("Inizio Associazione Prodotti");       
//$this->_hlp->debug($linkArray);
//$this->_hlp->debug($cardArray);
        $this->_linkProduct($linkArray, $cardArray);
        $this->_hlp->debug("Fine Associazione Prodotti");
        $this->_hlp->debug("Ricostruzione Indici");
return 0;        
        foreach ($_oriProcessor as $k=>$v) {
            $pProcess = Mage::getModel('index/process')->Load($k);            
            $pProcess->setMode($v)->save();
            $this->_hlp->debug("Ricalcolo " . $pProcess->getIndexerCode());
            $pProcess->reindexAll();
            $this->_hlp->debug("Ricalcolo " . $pProcess->getIndexerCode() . " Completato");
        }
        $this->_hlp->debug("Fine Ricostruzione Indici"); 
        
        $this->_hlp->debugImport(Autel_Catalog_Helper_Data::DEBUG_ACTION_END);
        return 0;
    }    
    
    /**
     * Controlla se i dati che provengono dal client sono sufficienti per creare il prodotto
     * @param object $prod Prodotto che arriva dal client
     * @return boolean true i dati sono corretti/false i dati non sono corretti
     */
    protected function _isDataComplete($prod) {
        $ret = true;
        if (is_null($prod->Sku)) {
            $this->_hlp->debug("Warning - Controllo prodotti: Sku non assegnato");
        }
                
        if ( is_null($prod->Type) || ($prod->Type !='simple' && $prod->Type!== 'configurable')) {              
            $this->_hlp->debug("Warning - Controllo prodotti: Tipo prodotto (".$prod->Type.") per ".$prod->Sku ."non valido" );
        }
        if (is_null($prod->AttributeSetId)) {
                $ret = "Verfica Prodotto: Set Attributi non Assegnato";
                $this->_hlp->debug("Error - $ret"); 
        }
        return $ret;
    }
    
    
    protected function _fastCreateProduct($prod) {
        
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        
        $this->_entityTypeId = Mage::getresourceModel('catalog/product')->getTypeId();
        
        $product = $this->_dbRead->fetchRow($this->_dbRead->select()
                                    ->from($this->_table['product'])
                                    ->where('sku = ?', $prod->Sku)); 
        
        $_isConfigurable = ($prod->Type == 'simple' && !is_null($prod->SkuConfigurable) && $prod->SkuConfigurable != "");
        $_entityId = 0;
	$_isNew = false;
        if (!is_array($product)) {
            $this->_dbWrite->insert($this->_table['product'], 
                                        array('attribute_set_id' => $prod->AttributeSetId,
                                              'entity_type_id'   => $this->_entityTypeId,
                                              'sku'              => $prod->Sku,
                                              'type_id'          => $prod->Type,
                                              'has_options'      => ($prod->Type=='configurable') ? 1: 0,
                                              'required_options' => ($prod->Type=='configurable') ? 1: 0,));
            $tab = $this->_dbRead->fetchRow("SHOW TABLE STATUS LIKE  '". $this->_table['product'] ."'");
            $_entityId = $tab['Auto_increment'] - 1;
    	    $_isNew = true;
        } else {
            $_entityId = $product['entity_id'];
        }
        
        $this->_dbWrite->beginTransaction();
        try {
            
            //Visibilità
            if ($prod->Type == 'simple' && $_isConfigurable) {
                $this->_updateAttribute ($_entityId, 'visibility', 0, Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
            } else {
                $this->_updateAttribute ($_entityId, 'visibility', 0, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH); 
            }

            $this->_updateAttribute ($_entityId, 'tax_class_id', 0, $this->_getTax());
                        
            $Store0Attribute = arraY();
            foreach ($prod->Attribute as $attribute) {
                
                if ($_isNew || $prod->ForceUpdateAll == 2 ||
                   ($prod->ForceUpdateAll == 1 && array_search($attribute->AttributeCode, $this->_forceUpdate) === false) ||
                   (array_search($attribute->AttributeCode, $this->_allUpdate)) !== false){
                                                        
                    $attrValue = null;
                
                    $actRow = $this->_getAttributeRow($attribute->AttributeCode);

                    if ($actRow->getIsUserDefined() && 
                       ($actRow->getFrontendInput() == "select" || $actRow->getFrontendInput() == "multiselect" )) {
                            //Cerco il valore tra le opzione 
                        foreach (explode(",", $attribute->AttributeValue) as $_attVal) {
                             $_attrValue = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                            ->setStoreFilter(0)
                                            ->setAttributeFilter($actRow->getId())
                                            ->addFieldToFilter(' tdv.value ', $_attVal) 
                                            ->getFIrstItem()->getId();
                            if (is_null($_attrValue) || $_attrValue == 0) {
                                 //Creo l'attributo per lo store
                            }
			if (!is_null($attrValue))
                             $attrValue.=",";
                         
			$attrValue.=$_attrValue;
                     }
                    } else {
                        $attrValue = $attribute->AttributeValue;                    
                    }  
                    
                    if ($attribute->AttributeCode == "status" && $_isConfigurable) {
                        //Per l'attributo status nel caso di un semplice associato al conifugrabile lo metto sempre attibuo
                        $attrValue = Mage_Catalog_Model_Product_Status::STATUS_ENABLED; 
                    }

//			$this->_hlp->debug("Agg " .$prod->Sku. ". Agg Attr " . $attribute->AttributeCode);
                    $this->_updateAttribute ($_entityId, $attribute->AttributeCode, $attribute->StoreId, $attrValue);
                    if ($attribute->StoreId === 0) {
                        $Store0Attribute[] = $attribute->AttributeCode;
                    } else {
                        if ( !in_array($attribute->AttributeCode, $Store0Attribute)) {
                            $this->_updateAttribute ($_entityId, $attribute->AttributeCode, 0, $attrValue, false);
                            $Store0Attribute[] = $attribute->AttributeCode;
                        }
                    }                        
                }
            }
            
            if ($_isNew || $prod->ForceUpdateAll >= 1 || $prod->ForceWebSites) {
                //Web Site Disassocio e Riassocio
                $this->_dbWrite->delete($this->_table['website'], array('product_id = ?' => $_entityId));
                foreach ($this->_hlp->getWebSite($prod->WebSite) as $ws) {
                    $this->_dbWrite->insert($this->_table['website'], array('product_id' => $_entityId, 'website_id' => $ws));
                }
            }
            
            //Le categorie le  aggiorno solo se il prodotto non è semplice o non ha il riferimento
            if ($_isNew && ($prod->Type != 'simple' || !$_isConfigurable)) {
                    $this->_dbWrite->delete($this->_table['category'], array('product_id = ?' => $_entityId));
                    foreach ($this->_catHlp->getCategory($prod->Category) as $k=>$v) {                
			foreach ($v as $cat)
	                        $this->_dbWrite->insert($this->_table['category'], array('product_id' => $_entityId, 'category_id' => $cat));
                    }
            }
            
            //Update delle quantità
            foreach ($prod->Stock as $stock) {

                    $this->_updateQta($_entityId, ((is_null($stock->Qty) || ($prod->Type != 'simple'))?0:$stock->Qty), (is_null($stock->OutOfStock || $stock->OutOfStock == 1)?0:1),$this->_hlp->getWebSite($prod->WebSite));
            }
        
            $this->_dbWrite->commit();
        } catch (Exception $ex) {
            $this->_dbWrite->rollBack();
            Mage::LogException($ex);
            $this->_hlp->debug("Errore in fase di creazione dell'articolo");
            $this->_hlp->debug($ex);
        }
        
        return $_entityId;

        
    }
    
    private function _updateQta($entityId, $qta, $status, $webSites) {
        
        $sql  = "INSERT INTO " . $this->_table['stock'] . "(product_id, stock_id, qty, is_in_stock) VALUES " ;
        $sql .= "($entityId, 1, $qta, $status) ";
        $sql .= " ON DUPLICATE KEY UPDATE qty = $qta, is_in_stock = $status";
        
        $this->_dbWrite->query($sql);
        
/*        foreach ($webSites as $ws) {
            $sql  = "INSERT INTO " . $this->_table['stockstatus'] . "(product_id, website_id, stock_id, qty, stock_status) VALUES " ;
            $sql .= "($entityId, $ws, 1, $qta, $status) ";
            $sql .= " ON DUPLICATE KEY UPDATE qty = $qta, stock_status = $status";
        
            $this->_dbWrite->query($sql);
        }*/
        
        
    }
    
    private function _updateAttribute($entityId, $name, $store, $value, $force = true) {

        $attr = $this->_getAttributeRow($name);

	if (!$attr->hasAttributeId()) {	
		$this->_hlp->debug("Attributo non valido $name");
		return;
	}
        $field = array('entity_type_id' => $this->_entityTypeId,
                       'attribute_id'   => $attr->getAttributeId(),
                       'entity_id'      => $entityId);

        $tableName = $this->_getTableName($attr->getBackendType());                                    

        if ($store === 0 && $force) {
            //Se richiedo lo store 0 azzero tutti gli altri
	    $deleteWhere = array();
            foreach ($field as $f => $v) {
		$delteWhere["$f = ?"] = $v;
	    }
	    if (sizeof($deleteWhere) > 0) {
	        $delteWhere['store_id <> ?'] = $store;
        	$this->_dbWrite->delete($tableName, $delteWhere);
	    }
        }
        
        $field['store_id'] = $store;
        $field['value'] = $value;        
        
        $sql  = "INSERT INTO $tableName (entity_type_id, attribute_id, entity_id, store_id, value) VALUES " ;
        $sql .= "(?, ?, ?, ?, ?) ";
        $sql .= " ON DUPLICATE KEY UPDATE value = ?";

        $this->_dbWrite->query($sql, array_merge(array_values($field), array_values(array("value" => $value))));
        
    }
    
    private function _getTableName($attrType) {
        if (!isset($this->_table['productentity'][$attrType])) {
            $this->_table['productentity'][$attrType] = Mage::getSingleton('core/resource')->getTableName("catalog_product_entity_$attrType");
        }        
        return $this->_table['productentity'][$attrType];
    }
    
    /**
     * Creazione del prodotto che proviene dal client
     * @param object $prod Prodotto che arriva dal client
     * @return int Id del prodotto creato/modificato. Se si verifica un errore ritorna l'errore
     */
    protected function _createProduct($prod) {
        
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        
        //Leggo i prodotti per store
        $myProduct = $this->_hlp->getProduct4Store($prod->Sku);        
        
        $this->_hlp->debug("Aggiornamento Prodotto " .$prod->Sku . " - Type: " . $prod->Type);
        
        $myProduct[0]->setTypeId($prod->Type);
	$myProduct[0]->setProductTypeId($prod->Type);
        if ($prod->Type == 'simple' && !is_null($prod->SkuConfigurable) && $prod->SkuConfigurable != "")
            $myProduct[0]->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        else 
            $myProduct[0]->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH); 

        $myProduct[0]->setSku($prod->Sku);            
        $myProduct[0]->setAttributeSetId($prod->AttributeSetId);
        
        foreach ($prod->Attribute as $attribute) {
                        
//            $this->_hlp->debug("Aggiornamento Prodotto " .$prod->Sku. ". Aggiorno Attributo " . $attribute->AttributeCode );
            if ($myProduct[-1] == "insert" || $prod->ForceUpdateAll == 2 ||
               ($prod->ForceUpdateAll == 1 && array_search($attribute->AttributeCode, $this->_forceUpdate) === false) ||
               (array_search($attribute->AttributeCode, $this->_allUpdate)) !== false){
                
                $this->_hlp->debug("Aggiornamento Prodotto " .$prod->Sku. ". Aggiorno Attributo " . $attribute->AttributeCode );
                $attrValue = null;
                
                $actRow = $this->_getAttributeRow($attribute->AttributeCode);
                if ($actRow->getIsUserDefined() && 
                   ($actRow->getFrontendInput() == "select" || $actRow->getFrontendInput() == "multiselect" )) {
                    //Cerco il valore tra le opzione 
                     foreach (explode(",", $attribute->AttributeValue) as $_attVal) {
                         $_attrValue = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                        ->setStoreFilter($attribute->StoreId)
                                        ->setAttributeFilter($actRow->getId())
                                        ->addFieldToFilter(' tdv.value ', $_attVal) 
                                        ->getFIrstItem()->getId();
                         if (is_null($_attrValue) || $_attrValue == 0) {
                             //Creo l'attributo per lo store
                         }
                         if (!is_null($attrValue))
                             $attrValue.=",";
                         $attrValue.=$_attrValue;
                     }
                } else {
                    $attrValue = $attribute->AttributeValue;
                }  

//$this->_hlp->debug(($attribute->StoreId == 0) ? "vero" : "Falso");
//$this->_hlp->debug((!$myProduct[0]->hasData($attribute->AttributeCode)) ? 'Vero' : 'Falso');
//$this->_hlp->debug((($myProduct[0]->getData($attribute->AttributeCode) .'') == "") ? "Vero" : "Falso");
                
                $myProduct[$attribute->StoreId]->setData($attribute->AttributeCode,$attrValue);
                if ($attribute->StoreId == 0 || !$myProduct[0]->hasData($attribute->AttributeCode) || ($myProduct[0]->getData($attribute->AttributeCode) .'') == '') {
                    //Lo store di default non ha impostato l'attributo o
                    //l'attributo è per lo store di default o  
                    // lo store di default ha impostato l'attributo vuoto
                    $myProduct[0]->setData($attribute->AttributeCode,$attrValue);
//$this->_hlp->debug('Imposto store 0 - ' . $attrValue);
                }
            }
        }

        $this->_hlp->debug("Aggiornamento Prodotto " .$prod->Sku. ". Aggiorno WebSite "); 
        if ($myProduct[-1] == "insert" || $prod->ForceUpdateAll >= 1 || $prod->ForceWebSites) {
            $myProduct[0]->setWebsiteIDs($this->_hlp->getWebSite($prod->WebSite));
        }
	//Le categorie le  aggiorno solo se il prodotto non è semplice o non ha il riferimento
	if ($prod->Type != 'simple' || $prod->SkuConfigurable == "") {
	        $this->_hlp->debug("Aggiornamento Prodotto " .$prod->Sku. ". Aggiorno Category ");
        	//$this->_catHlp = Mage::Helper("autelcatalog/product_category");
	        foreach ($this->_catHlp->getCategory($prod->Category) as $k=>$v) {                
        	    $myProduct[$k]->setCategoryIds($v);
	        }
	}

        $this->_hlp->debug("Aggiornamento Prodotto " .$prod->Sku. ". Aggiorno Stock ");
        //Aggiorno a priori la quantità
        foreach ($prod->Stock as $stock) {

            if ($prod->Type == 'simple') {
                /**
                 * @todo c'è cmq da verificare correttamente la gestione dell'out of stock
                 */
                $myProduct[$stock->StoreId]->setStockData(array(
                    'is_in_stock' => is_null($stock->OutOfStock || $stock->OutOfStock == 1)?0:1,
                    'qty' => is_null($stock->Qty)?0:$stock->Qty,
                    'manage_stock' => 0,
//                    '' => is_null($stock->QtyMin)?0:$stock->QtyMin,
//                    '' => is_null($stock->QtyMinSales)?0:$stock->QtyMinSales,
//                    '' => is_null($stock->QtyMaxSales)?0:$stock->QtyMaxSales,
                    ));

            } else {
                $myProduct[$stock->StoreId]->setStockData(array(
                        'is_in_stock' => is_null($stock->OutOfStock || $stock->OutOfStock == 1)?0:1,
                        'manage_stock' => 0));
            }
        }
        try {                        
            $myProduct[0]->setTaxClassId($this->_getTax($myProduct[0]->getTaxClassId()));            

            $this->_hlp->debug("Aggiornamento Prodotto " .$prod->Sku. ". tentativo di Salvataggio Store 0 ");
            $myProduct[0]->Save();            

//$this->_hlp->debug(MAge::GetModel('catalog/product')->Load( $myProduct[0]->getId())->getData('c_weight_gr'));
            //Salvo solo per gli store previsti dai Web Site         
            foreach ($this->_hlp->getStoreByWebSite($prod->WebSite) as $store) {

                $this->_hlp->debug("Aggiornamento Prodotto " .$prod->Sku. ". tentativo di Salvataggio Store " . $store->getId());
                
		//Sistemo gli attributi imporatnti per evitare casini
		$myProduct[$store->getId()]->setTypeId($myProduct[0]->getTypeId());
		$myProduct[$store->getId()]->setWeight($myProduct[0]->getWeigth());

		foreach ($prod->Attribute as $attr) {
                    if ($attr->StoreId == 0) {
                    		$attrRow = $this->_getAttributeRow($attr->AttributeCode);
//$this->_hlp->debug($attrRow->getData());
                    		if ($attrRow->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL) {
                    			$myProduct[$store->getId()]->setData($attr->AttributeCode, $myProduct[0]->getData($attr->AttributeCode));
                    		} else {
                    			$myProduct[$store->getId()]->setData($attr->AttributeCode, false);                      
                    		}                        
                    }
                }                
                
                //$myProduct[$store->getId()]->setTaxClassId($this->_getTax($myProduct[$store->getId()]->getTaxClassId()));
                $myProduct[$store->getId()]->Save();

//$this->_hlp->debug(MAge::GetModel('catalog/product')->Load( $myProduct[0]->getId())->getData('c_weight_gr'));
                //Aggiorno l'associazione con il website
                //$this->_action->updateWebsites($myProduct[$store->getId()]->getId(), $store->getWebsiteId(), 'add');
            }            
        } catch (Exception $e) {
            $error = "Creazione Prodotto: Errore in salvataggio Sku: " . $prod->Sku . "\n" . $e->getMessage() . "\n" . $e->getTrace();
            $this->_hlp->debug("Errore - $error");
            return $error;
        }
         
        return $myProduct[0]->getId();
    }
    
    /**
     * Effettua l'associazione tra prodotto configurabile e semplice se non esite 
     * @param type $link 
     * @param type $card 
     */
    protected function _linkProduct($link, $card=array()) {
                
        foreach($link as $k=>$v) {
            $idParent = Mage::getModel('catalog/product')->getIdBySku($k);
            if ($idParent > 0) {
                foreach ($v as $l) {
                    $this->_Associated($idParent, $l['id']);                    
                }
                //Piazzo le cardinalità
                $ii = 0;

                if (sizeof($card) == 0) {
                    $_card = $this->_getCardinalty(Mage::getModel('catalog/product')->Load($idParent)->getAttributeSetId());
                } else {
                    foreach ($card[$k] as $c) {
                        $_cardAttribute = $this->_getAttributeRow($c["attributeCode"])->getId();
			if (!$_cardAttribute) {
	                        throw new Exception("Errore in fase di Associazione Prodotti! Non esite l'attributo " .$c["attributeCode"] . " usato nella cardinalità!");
			}
			$_card[] = $_cardAttribute;
                    }
                }              
                foreach ($_card as $idAttr) {
                    $select = $this->_dbRead->select()
                                   ->from(Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute'))
                                   ->where('product_id = ?', $idParent)
                                   ->where('attribute_id = ?', $idAttr);                    
                    if ($this->_dbRead->fetchOne($select)."" == "") {
                        $this->_dbWrite->insert(Mage::getSingleton('core/resource')->getTableName('catalog_product_super_attribute'),
                                            array("product_id"   => $idParent,
                                                  "attribute_id" => $idAttr,
                                                  "position"     => $ii++));
                    }
                }
                
            }
        }        
    }
    
    protected function _Associated($idParent, $idChild) {
        
        //Relazione
        $select = $this->_dbRead->select()
                       ->from(Mage::getSingleton('core/resource')->getTableName('catalog_product_relation'))
                       ->where('parent_id = ?', $idParent)
                       ->where('child_id = ?', $idChild);
        if ($this->_dbRead->fetchOne($select)."" == "") {
            $this->_dbWrite->Insert(Mage::getSingleton('core/resource')->getTableName('catalog_product_relation'),
                                    array('parent_id' => $idParent,
                                          'child_id'  => $idChild));
        }
        
        //Super Links
        $select = $this->_dbRead->select()
                       ->from(Mage::getSingleton('core/resource')->getTableName('catalog_product_super_link'))
                       ->where('parent_id = ?', $idParent)
                       ->where('product_id = ?', $idChild);
        if ($this->_dbRead->fetchOne($select)."" == "") {
            $this->_dbWrite->Insert(Mage::getSingleton('core/resource')->getTableName('catalog_product_super_link'),
                                    array('parent_id' => $idParent,
                                          'product_id'  => $idChild));
        }
        
    }
    
    protected function _getCardinalty($attributeSetId) {
        if (!isset($this->_cardinality[$attributeSetId])) {
            foreach($this->_hlp->getCardinalityByAttrSetId($attributeSetId)as $row) {
                $this->_cardinality[$attributeSetId][] = $row["attributeId"];
            }
        }

        return $this->_cardinality[$attributeSetId];
    }
        
    protected function _getAttributeRow($name) {
        if (!isset($this->_attributeRow[$name])) 
            $this->_attributeRow[$name] = Mage::getResourceModel('eav/entity_attribute_collection') //Recuper l'id dell'attributo
                            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                            ->addFieldToFilter('attribute_code', $name) 
                            ->load()
                            ->getFirstItem();                

        return $this->_attributeRow[$name];
    }
    
    private function _getTax($taxId=null) {
        if (is_null($taxId))
            $taxId = Mage::getStoreConfig('autelconnector/connector_product/default_taxclass', 0);
        return $taxId;
    }
    
    
    /************************************** Import Prezzo ****************************************/
    
    /**
     * API per l'aggionrnamento del prezzo
     * @param ZIP JSON $input
     * @return type
     */
    public function importprice($input) {
        
        $str =  base64_decode($input);

        return $this->_importPrice (gzinflate($str));
    }
    
    /**
     * Aggiornamento del prezzo
     * @param JSON $input
     */
    protected function _importPrice($input) {

        $productList = json_decode($input);
        
        
        try {
            foreach ($productList as $product) {

                if ($product->Type == 'simple' && !is_null($prod->SkuConfigurable) && $prod->SkuConfigurable != "") {
                    //Prodotto semplice legato al configurabile non aggiorno il prezzo 
                    //@Todo Da gestire
                    continue;
                } else {
                    //Prodotto semplice o configurabile, aggiorno l'attributo price

                    $this->_entityTypeId = Mage::getresourceModel('catalog/product')->getTypeId();

                    $prod = $this->_dbRead->fetchRow($this->_dbRead->select()
                                        ->from($this->_table['product'])
                                        ->where('sku = ?', $prod->Sku)); 
                    if (is_array($prod)) {
                        $_entityId = $prod['entity_id'];

                        $Store0Attribute = false;
                        foreach ($prod->Attribute as $attribute) {
                            //Aggiorno l'attributo price 
                            // @todo gestione dello special e del teir price
                            if ($attribute->AttributeCode == 'price') {                                
                                $this->_dbWrite->BeginTransaction();
                                $this->_updateAttribute ($_entityId, $attribute->AttributeCode, $attribute->StoreId, $attribute->AttributeValue);
Mage::Log('Ho aggironato il prezzo per ' . $prod->Sku );
MAge::log( $attribute);
                                if ($attribute->StoreId === 0 || $Store0Attribute) {
                                    $Store0Attribute = true;
                                } else {
                                    $this->_updateAttribute ($_entityId, $attribute->AttributeCode, 0, $attribute->AttributeValue, false);
                                    $Store0Attribute = true;
                                }                        
                                $this->_dbWrite->commit();
                            }
                        }
                        
                    }

                }
            } 
        } catch (Exception $ex) {
            $this->_dbWrite->rollBack();
            Mage::LogException($ex);
            return false;
        }
        
        //Ricostruisco gli indici relativi al prezzo
        $pProcess = Mage::getModel('index/process')->Load("catalog_product_price", 'indexer_code');
        $pProcess->reindexAll();
        return true;
    }
    
    
    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     * In case of configurable product get child's sku list
     *
     * @param null|object|array $filters
     * @param string|int $store
     * @return array
     */
    public function getitems($filters = null, $store = null) {
    
        $products = parent::items($filters, $store);        
        
        foreach ($products as $idx => $prod) {
            $products[$idx]['sku_childs'] = array();
            switch ($prod["type"]) {
                case "configurable":
                    $p = Mage::getModel('catalog/product')->Load($prod['product_id']);
                    $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProductCollection($p);
                    foreach ($childProducts as $child) {
                        $products[$idx]['sku_childs'][] = $child->getSku();
                    }
                    break;
                case "grouped":
                case "bundle":
                    //Todo
                default:
                    break;
            }
        }
        
        return $products;
    }
    
}

