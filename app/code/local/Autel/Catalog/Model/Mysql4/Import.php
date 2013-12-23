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
class Autel_Catalog_Model_Mysql4_Import extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('autelcatalog/import', 'entity_id');
    }
   
}

?>
