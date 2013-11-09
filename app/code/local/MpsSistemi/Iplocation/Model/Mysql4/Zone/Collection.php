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
class MpsSistemi_Iplocation_Model_Mysql4_Zone_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{    

    private $_getByGoup = false;
    
    protected function _construct()
    {
            $this->_init('mpslocation/zone');
    }
    
    public function Sort($order = "ASC") {
        if (!$this->_getByGoup) {
            $this->getSelect()->order('sort_order',$order);
        } else {
            $this->getSelect()->order('group.sort_order',$order)
                              ->order('main_table.sort_order',$order);
        }
        return $this;
    } 
    
    
    public function getByGroup() {
        
        $this->getSelect()
             ->join(array('group' => Mage::getSingleton('core/resource')->getTableName('mpslocation/zonegroup')),
                          'main_table.group_id = group.entity_id', 
                           array("group_name", "group.sort_order as sort_order_group"));
        $this->_getByGoup = true;
        return $this;
    }
}

?>
