<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zone
 *
 * @author marcoma
 */
class MpsSistemi_Iplocation_Model_Mysql4_Zone extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('mpslocation/zone', 'entity_id');
    }

    public function getStoreLabels($zoneId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('mpslocation/zonelabel'), array('store_id', 'label'))
            ->where('zone_id=?', $zoneId);
        return $this->_getReadAdapter()->fetchPairs($select);
    }
    
    public function getStoreLabel($zoneId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('mpslocation/zonelabel'), array('label'))
            ->where('zone_id=?', $zoneId)
            ->where('store_id in (?)', array($storeId, 0))
            ->order('store_id desc');
        return $this->_getReadAdapter()->fetchOne($select);
    }
    
    public function saveStoreLabels($zoneId, $labels){
        $delete = array();
        $save = array();
        $table = $this->getTable('mpslocation/zonelabel');
        $adapter = $this->_getWriteAdapter();

        foreach ($labels as $storeId => $label) {
            if (Mage::helper('core/string')->strlen($label)) {
                $data = array('zone_id' => $zoneId, 'store_id' => $storeId, 'label' => $label);
                $adapter->insertOnDuplicate($table, $data, array('label'));
            } else {
                $delete[] = $storeId;
            }
        }      
        if (!empty($delete)) {
            $adapter->delete($table,
                $adapter->quoteInto('zone_id=? AND ', $zoneId) . $adapter->quoteInto('store_id IN (?)', $delete)
            );
        }
        return $this;
        
    }    

}

?>
