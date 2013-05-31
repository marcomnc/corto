<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author marcoma
 */
class Autel_Corto_Model_Mysql4_Faq_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{    

    protected function _construct()
    {
        $this->_init('autelcorto/faq');
            
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
        $this->_map['fields']['store']   = 'store_table.store_id';
    }
    
    public function Sort($order = "ASC") {
        $this->getSelect()->order('sort_order',$order);
        return $this;
    } 
    
        /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            if (!is_array($store)) {
                $store = array($store);
            }

            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }

            $this->addFilter('store', array('in' => $store), 'public');
        }
        return $this;
    }
    
    /**
     * Join store relation table if there is store filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('autelcorto/faq_store')),
                'main_table.entity_id = store_table.page_id',
                array()
            )->group('main_table.entity_id');

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }

    /**
     * Perform operations after collection load
     *
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    protected function _afterLoad()
    {

        $items = $this->getColumnValues('entity_id');
        
        $connection = $this->getConnection();
        if (count($items)) {

            $select = $connection->select()
                    ->from(array('cps'=>$this->getTable('autelcorto/faq_store')))
                    ->where('cps.faq_id IN (?)', $items);

            foreach ($this as $item) {
                
                if ($result = $connection->fetchPairs($select)) {
                    if (isset($result[$item->getData('entity_id')])) {
                        if ($result[$item->getData('entity_id')] == 0) {
                            $stores = Mage::app()->getStores(false, true);
                            $storeId = current($stores)->getId();
                            $storeCode = key($stores);
                        } else {
                            $storeId = $result[$item->getData('entity_id')];
                            $storeCode = Mage::app()->getStore($storeId)->getCode();
                        }
                        $item->setData('_first_store_id', $storeId);
                        $item->setData('store_code', $storeCode);
                    }
                }
                
                $select = $connection->select()
                    ->from(array('cps'=>$this->getTable('autelcorto/faq_store')))
                    ->where('cps.faq_id IN (?)', $item->getId());
                $store = array();
                foreach ($connection->fetchAll($select) as $st) {
                    $store[] = $st['store_id'];
                }
                if (sizeof($store) == 0) {
                    $store = Mage_Core_Model_App::ADMIN_STORE_ID;
                }
                $item->setData('store_id', $store);
                $item->setData('faq_serial', unserialize($item->getData('faq_serial')));
            }
            
        }           
        
        return parent::_afterLoad();
    }
    
}

?>
