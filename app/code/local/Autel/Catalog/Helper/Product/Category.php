<?php

/**
 * Helper per l'importazione del prodotto
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Helper_Product_Category extends Autel_Catalog_Helper_Data 
{    
    protected $_rootCategroy = "2";
    protected $_rootCategoryPath = array(1,2);
    /**
     * Recupero le categorie da un array. Se la categoria non esiste la creo sotto la root.
     * Se previsto. Considero l'array come la struttura ad alebero
     * 
     * ATTENZIONE!!! Per come è strutturato ora è necessario che lo store sia sempre 0!!
     * 
     * @param array $category contiene un oggetto con Key/Value, dove key è il ramo
     */
    public function getCategory($category=array(), $isTree = false) {
        
        $retValue = array();
        
        if (!is_array($category) && is_object($category)) {
            $category = array($category);
        } 

        $catStoreSerialize = array();
        $tree = Array();
        foreach ($category as $catObj ) {
            if (is_object($catObj)) {
                $cat = $this->_getCategoryByName($catObj->Category, $catObj->Father);
                if (is_null($cat) || $cat->getId() <= 0) {
                    $myCat = new Mage_Catalog_Model_Category();
                    $myCat->setStoreId($catObj->StoreId)
                          ->setAttributeSetId($myCat->getDefaultAttributeSetId())
                          ->setPath(implode('/',$this->_rootCategoryPath))
                          ->setParent($this->_rootCategroy)                                            
                          ->setIncludeInMenu(0)
                          ->setName($catObj->Category)
                          //->setDescription($catObj->Category) Parametrizzare
                          ->setIsActive(1);
                    $myCat->Save();
                    $cat = Mage::getModel("catalog/category")->getCollection()
                                ->AddAttributeToFilter("name", $catObj->Category)
                                ->getFirstItem();                    
                }
                
                if ($catObj->Father != null && $catObj->Father != "" &&
                    $catObj->Father != Mage::getModel("catalog/category")->Load($cat->getParentId())->getName()) {             
                    //Soposto la categoria sotto il padre indicato....
                    $_parentCat = $this->_getCategoryByName($catObj->Father)->getId();
                    if ($_parentCat > 0)
                        $cat->move($_parentCat, null);
                }
                $catStoreSerialize[$catObj->StoreId][] = $cat->getId();
            }
        }
        
        foreach ($catStoreSerialize as $store => $cat) {
            $retValue[$store] = $cat;
        }
        return $retValue;
    }
    
    protected function _getCategoryByName ($name, $father = "") {
        
        $cat =  Mage::getModel("catalog/category")->getCollection()
                            ->AddAttributeToFilter("name", $name);

        
        if (($father."") == "") {
            return $cat->getFirstItem();
        }
            
        foreach ($cat as $c) {
            if ($father == Mage::getModel("catalog/category")->Load($c->getParentId())->getName()) {
                return $c;
            }
        }

        return null;
    }
    
//    private function _getRootId($storeId = null) {        
//        if (is_null($storeId) || $storeId == 0) {
//            $storeId = Mage::getModel("core/website")->getCollection()
//                        ->AddAttributeToFilter("is_default = 1")
//                        ->getFirstItem()
//                        ->getId();
//        }
//        $ws = Mage::getModel("core/website")->Load($storeId);
//        if (!$ws->hasWebsiteId()) {
//            $ws = Mage::getModel("core/website")->Load(Mage::getModel("core/website")->getCollection()
//                                                        ->AddAttributeToFilter("is_default = 1")
//                                                        ->getFirstItem()
//                                                        ->getFirstItem());
//        }
//Mage::Log($ws);
//        return $ws->getRootCategoryId();
//    }
}

?>
