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
class Autel_Corto_Model_Mysql4_Faq extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('autelcorto/faq', 'entity_id');
    }

    /**
     * Process page data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'entity_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('autelcorto/faq_store'), $condition);

        return parent::_beforeDelete($object);
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        
        if (is_array($object->getFaqSerial())) {
            $serial = $object->getFaqSerial();
            unset($serial['__empty']);
            $object->setFaqSerial(serialize($serial));
//            echo "<pre>";
//            print_r($object);
//            die();
        }
        return parent::_beforeSave($object);
    }

    /**
     * Processing object after load data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('faq_serial', unserialize($object->getData('faq_serial')));  
        
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $connection->select()
                ->from(array('cps'=>$this->getTable('autelcorto/faq_store')))
                ->where('cps.faq_id IN (?)', $object->getId());
        $store = array();
        foreach ($connection->fetchAll($select) as $st) {
            $store[] = $st['store_id'];
        }
        
        if (sizeof($store) == 0) {
            $store = Mage_Core_Model_App::ADMIN_STORE_ID;
        }
        
        $object->setData('store_id', $store); 
        
        //leggo gli store collegati
        
//            echo "<pre>";
//            print_r($object);
//            die();
        return parent::_afterLoad($object);
    }

    
    /**
     * Assign FAQ to store views
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStores();
        }
        $table  = $this->getTable('autelcorto/faq_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'faq_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'faq_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    public function lookupStoreIds($FaqId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('autelcorto/faq_store'), 'store_id')
            ->where('faq_id = ?',(int)$FaqId);

        return $adapter->fetchCol($select);
    }
    
}

?>
