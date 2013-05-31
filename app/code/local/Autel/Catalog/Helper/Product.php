<?php

/**
 * Helper per l'importazione del prodotto
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Helper_Product extends Autel_Catalog_Helper_Data 
{
    /**
     * Dato un set di attributi recupero tutti gli attributi che ne fanno la cardinalità
     * @param int $attributeSetId 
     * @return array Array di atributi strutturati come attribute_id, attribute_code
     */
    public function getCardinalityByAttrSetId($attributeSetId) {

        $select = $this->_dbRead->select()
                ->from(array('ca' => Mage::getSingleton('core/resource')->getTableName('catalog_eav_attribute')))
                ->join(array('ea' => Mage::getSingleton('core/resource')->getTableName('eav_attribute')),
                       "ea.attribute_id = ca.attribute_id")
                ->join(array('ent' => Mage::getSingleton('core/resource')->getTableName('eav_entity_attribute')),
                       "ent.attribute_id = ea.attribute_id")
                ->where("ca.is_visible =    1")
                ->where("ca.is_configurable = 1")
                ->where("ca.is_global = 1")
                ->where("ea.frontend_input = 'select'")
                ->where("ent.attribute_set_id = $attributeSetId")
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('AttributeId'   => 'ea.attribute_id',
                                'Code' => 'ea.attribute_code'));                       
        return $this->_dbRead->FetchAll($select);
    }
    
    /**
     * Dato un set di attributi recupero tutti gli attributi che ne fanno la cardinalità
     * @param string $attributeSet
     * @return array Array di atributi strutturati come attribute_id, attribute_code 
     *               false se non riesce a recuperare l'attribiuteSet
     */
    public function getCardinalityByAttrSet($attributeSet) {
         
        $attrSetId = Mage::getModel('eav/_attribute_set')
                        ->getCollection()
                        ->AddAttributeToFilter('entity_type_id', Mage::getModel('catalog/product')->getResource()->getTypeId())
                        ->AddAttributeToFilter('attrbiute_set_name', $attributeSet)
                        ->getFirstItem()->getId();

        if ($attrSetId > 0) {
            return $this->getCardinalityByAttrSetId($sttrSetId);
        } else {
            return false;
        }
    }    
                    
    /**
     * Dato uno SKU recupero tutti gli attributi che ne fanno la cardinalità
     * @param string $ProductId
     * @return array Array di atributi strutturati come attribute_id, attribute_code 
     *               false se non riesce a recuperare il prodotto
     */    
    public function getCardinalityBySku($sku) {
        return $this->getCardinalityByProductId(Mage::getModel("catalog/product")->getIdBySku($sku));
    }

    /**
     * Dato un productId recupero tutti gli attributi che ne fanno la cardinalità
     * @param string $ProductId
     * @return array Array di atributi strutturati come attribute_id, attribute_code 
     *               false se non riesce a recuperare il prodotto
     */    
    public function getCardinalityByProductId($ProductId) {
        
        $product = Mage::getModel("catalog/product")->Load($productId);
        
        if ($product->getId() > 0) {
            return $this->getCardinalityByAttrSetId($product->getAttributeSetId());
        } else {
            return false;
        }
    }

    /**
     * Recupero l'articolo creando un array contenente lo stesso prodotto impostato per ogni store
     * @param string $sku
     * @return array
     */
    public function getProduct4Store($sku) {
        $prodArray = array();
        $id = Mage::getModel('catalog/product')->getIdBySku($sku);
        if ($id === false) {
            //Creo il prodotto come disabilitato
            $prod = new Mage_Catalog_Model_Product();
            $prod->setSku($sku);
            $prod->setAttributeSetId(Mage::getStoreConfig("autelconnector/connector_product/default_attributesetid"));
            $prod->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
            $prod->Save();
            $prodArray[-1] = "insert";
            $id = Mage::getModel('catalog/product')->getIdBySku($sku);
        } else {
            $prodArray[-1] = "update";
        }
        
        $prodArray[0] = Mage::getModel("catalog/product")->setStoreId(0)->Load($id);

        foreach (Mage::app()->getStores() as $store) {
                $prodArray[$store->getId()] = Mage::getModel("catalog/product")->Load($id)->setStoreId($store->getId());
        }
        return $prodArray;
    }
    
    public function getOptionValue ($attributeCode, $storeId) {        
        $select = $this->_dbRead->select()
                ->from(array('ca' => Mage::getSingleton('core/resource')->getTableName('catalog_eav_attribute')))
                ->join(array('ea' => Mage::getSingleton('core/resource')->getTableName('eav_attribute')),
                       "ea.attribute_id = ca.attribute_id")
                ->join(array('op' => Mage::getSingleton('core/resource')->getTableName('eav_attribute_option')),
                       "ea.attribute_id = op.attribute_id")
                ->join(array('vcode' => Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value')),
                       "op.option_id = vcode.option_id and vcode.store_id = 0")
                ->joinLeft(array('vstore' => Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value')),
                            "op.option_id = vstore.option_id and vstore.store_id = $storeId")                
                ->where("ea.attribute_code = ?", $attributeCode)
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('AttributeId'   => 'ea.attribute_id',
                                'AttributeCode' => 'ea.attribute_code',
                                'code' => 'vcode.value',
                                'description' => 'vstore.value',)); 
        return $this->_dbRead->FetchAll($select);
    }
    
    /**
     * Recupero i web site per cui è abilitato l'articolo verificandoli.  
     * Se i web site specificati non sono presenti assegno l'articolo a Web 
     * site di default
     * @param array $webSite 
     */
    public function getWebSite($webSite = Array()) {
        $returnWs = array();
        if (!is_array($webSite))
                $webSite = array($webSite);
        foreach ($webSite as $site) {
            $ws = Mage::getModel('core/website')->Load($site);
            if ($ws->hasWebsiteId()) {
                $returnWs[] = $site;
            }
        }
        if (sizeof($returnWs) == 0) {
            $returnWs[] = Mage::App()->getStore()->getWebsiteId();
        }        
        return $returnWs;
    }   
    
    /**
     * Recupero gli store per cui è abilitato l'articolo partendo dai sui web site
     * @param array $webSite 
     */
    public function getStoreByWebSite($webSite = Array()) {
        $returnWs = array();
        if (!is_array($webSite))
                $webSite = array($webSite);
        foreach ($webSite as $site) {
            $store = Mage::getModel('core/store')->getCollection()
            			->AddFieldToFilter("website_id", $site);
			foreach ($store as $s) {
            	$returnWs[] = $s;
            }
        }
        if (sizeof($returnWs) == 0) {
            $returnWs[] = Mage::App()->getStore()->getStoreId();
        }        
        return $returnWs;
    }   
}

?>
