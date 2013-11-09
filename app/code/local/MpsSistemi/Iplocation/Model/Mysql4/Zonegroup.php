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
class MpsSistemi_Iplocation_Model_Mysql4_Zonegroup extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('mpslocation/zonegroup', 'entity_id');
    }

    public function getName() {
        return $this->getGroupName();
    }
    
    public function setName($object) {
        $this->setGroupName($object);
    }
}

?>
