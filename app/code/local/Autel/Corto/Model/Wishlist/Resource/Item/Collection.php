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

class Autel_Corto_Model_Wishlist_Resource_Item_Collection extends Mage_Wishlist_Model_Resource_Item_Collection
{
    /**
     * Override della funzione perchè fa casini con il filtro per store.
     * Siccome corto non ne ha bisogno lo tolgo
     * @return \Autel_Corto_Model_Wishlist_Resource_Item_Collection
     */
    protected function _assignProducts()
    {
        Varien_Profiler::start('WISHLIST:'.__METHOD__);
        $productIds = array();

        $isStoreAdmin = Mage::app()->getStore()->isAdmin();

        $storeIds = array();
        foreach ($this as $item) {
            $productIds[$item->getProductId()] = 1;
            if ($isStoreAdmin && !in_array($item->getStoreId(), $storeIds)) {
                $storeIds[] = $item->getStoreId();
            }
        }
        if (!$isStoreAdmin) {
            $storeIds = $this->_storeIds;
        }

        $this->_productIds = array_merge($this->_productIds, array_keys($productIds));
        $attributes = Mage::getSingleton('wishlist/config')->getProductAttributes();
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        foreach ($storeIds as $id) {
            // Non faccio il filtro per store
            //$productCollection->addStoreFilter($id);
        }

        if ($this->_productVisible) {
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($productCollection);
        }

        $productCollection->addPriceData($this->_customerGroupId, $this->_websiteId)
            ->addTaxPercents()
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect($attributes)
            ->addOptionsToResult()
            ->addUrlRewrite();

        if ($this->_productSalable) {
            $productCollection = Mage::helper('adminhtml/sales')->applySalableProductTypesFilter($productCollection);
        }

        Mage::dispatchEvent('wishlist_item_collection_products_after_load', array(
            'product_collection' => $productCollection
        ));

        $checkInStock = $this->_productInStock && !Mage::helper('cataloginventory')->isShowOutOfStock();

        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                if ($checkInStock && !$product->isInStock()) {
                    $this->removeItemByKey($item->getId());
                } else {
                    $product->setCustomOptions(array());
                    $item->setProduct($product);
                    $item->setProductName($product->getName());
                    $item->setName($product->getName());
                    $item->setPrice($product->getPrice());
                }
            } else {
                $item->isDeleted(true);
            }
        }

        Varien_Profiler::stop('WISHLIST:'.__METHOD__);

        return $this;
    }

    
}
