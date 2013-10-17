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
class Autel_Corto_Model_Mysql4_Cms_Pageblocks_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{    

    protected function _construct()
    {
        $this->_init('autelcorto/cms_pageblocks');            
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
    
    public function Sort($order = "ASC") {
        $this->getSelect()->order('position',$order);
        return $this;
    } 
    
    /**
     * Add filter by store
     *
     * @param int|array $page
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    public function addPageFilter($page)
    {
        if (!is_array($page)) {
            $page = array($page);
        }
        
        $this->addFieldToFilter('page_id', array('in' => $page));

        return $this;
    }
    
}

?>
