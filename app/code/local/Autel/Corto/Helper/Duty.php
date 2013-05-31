<?php

/**
 *
 * @category    
 * @package     
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */
class Autel_Corto_Helper_Duty extends Autel_Corto_Helper_Data
{
    /**
     * 
     * @param int|string|Mage_Catalog_Model_Product $product
     * @return String|Bool Harmonized code o false
     */
    public function getCodeFromProduct($product, $to = null) {

        if (!$product instanceof Mage_Catalog_Model_Product) {
            $id = Mage::getModel('catalog/product')->getIdbySku($product) + 0;
            if ($id == 0 && is_numeric($product)) {
                $id = $product;
            }
            $product = Mage::getModel('catalog/product')->Load($id);
            if (is_null($product))
                return false;
        }

       $dcId = $this->getDutyId($product->getId());
       
       if ($dcId === false) {
           return false;
       }

       $params = array();
       $params['to'] = ($to."" == "") ? Mage::getStoreConfig('general/country/default') : $to;
       $params['province'] = '';
       $params['classify_by'] = 'cat';
       $params['cat'][0] = $product->getData('c_cites');
       $params['hs'][0] = '';
       $params['desc'][0] = '';// $product->getShortDescription();
       $params['sku'][0] = '';// $product->getSku();
       $params['detailed_result'] = 0;
       $helper = Mage::helper('dccharge');       
       $rawXml = $helper->sendRequest('get-hscode', $params);
       
       

       try {
           $xml = new SimpleXMLElement($rawXml);
           $hsCode = get_object_vars($xml->classification);
           return $hsCode['hs-code'];
       } catch (Exception $ex) {
           return false;
       }
        
    }
    
    /**
     * Verifico se il prodotto ha il duty
     * @param int $prodcutId
     * @return int|bool
     */
    public function getDutyId($productId) {
        
        $product = Mage::getModel('catalog/product')->Load($productId);
        
        //return (!is_null($product) && $product->hasDcProductId() && $product->getDcProductId() != "") ? $product->getDcProductId() : false; 
        
        return (!is_null($product) && $product->hasCCites() && $product->getCCites() != "") ? $product->getCCites() : false; 
        
    }
}

?>
