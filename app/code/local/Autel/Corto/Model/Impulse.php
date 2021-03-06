<?php


/**
 *
 * Funzione Impulse Buy
 *
 * @author      Marco Mancinelli
 */
class Autel_Corto_Model_Impulse extends Mage_Core_Model_Abstract           
{
    public function ImpulseBuy() {        
        $ret = false;
        
        $products = Mage::getModel("catalog/product")->getCollection();        
        $products->addFieldToFilter("status",array('in'=> array(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)))
                 ->addAttributeToFilter('visibility', array('in'=>array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)));
        $products->getSelect()
                ->joinleft(array('_links'=>Mage::getSingleton('core/resource')
                                           ->getTableNAme('catalog_product_super_link')),
                                  '_links.parent_id = e.entity_id')
                ->where("_links.product_id is null")
                ->order('rand()');

        foreach ($products as $product) {            
            if ($product->isSaleable() && $product->getStockItem()->getIsInStock()==Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK) {
                $prod = Mage::getModel("catalog/product")
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->Load($product->GetId());
                Mage::getSingleton("checkout/session")->setCartWasUpdated(true);
                $ret = $prod;
                break;
            }
        }
        
        return $ret;
    }
    
    public function ImpulseBuyAdd($product) {
        
        if (!$product instanceof Mage_Catalog_Model_Product) {
            $product = Mage::GetModel('catalog/product')->Load($product);            
        }
        
        if (!is_null($product)) {
            try {
                $cart = Mage::getSingleton('checkout/cart');
                $cart->addProduct($product,1);
                $cart->save();
                Mage::getSingleton("checkout/session")->setCartWasUpdated(true);
                return true;
            } catch (Exception $ex) {
                Mage::logException($ex);
            }
            
        }
        
        return false;
        
    }
    
}

?>
