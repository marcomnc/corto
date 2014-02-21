<?php


/**
 * Catalog Inventory
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */
class Autel_Catalog_Model_Cataloginventory_Stock_Item_Api_V2 extends Mage_CatalogInventory_Model_Stock_Item_Api_V2 {
    
    
    /**
     * Overrride della fuzione per fare l'update secco in SQL!!
     *
     * @param array $productIds
     * @param array $productData
     * @return boolean
     */
    public function multiUpdate($productIds, $productData)
    {
        if (count($productIds) != count($productData)) {
            $this->_fault('multi_update_not_match');
        }

        $productData = (array)$productData;

        foreach ($productIds as $index => $productId) {
            $sql  = "INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
            $sql .= "(product_id, stock_id, qty, is_in_stock) VALUES " ;
            $sql .= "($productId, 1, " . $productData[$index]->qty . ", " . $productData[$index]->is_in_stock .") ";
            $sql .= " ON DUPLICATE KEY UPDATE qty = " . $productData[$index]->qty . ", is_in_stock = " . $productData[$index]->is_in_stock;

            Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
        }

        //Ricostruisco gli indici relativi alle qtà
        $pProcess = Mage::getModel('index/process')->Load("cataloginventory_stock", 'indexer_code');
        $pProcess->reindexAll();
        return true;
    }
}
