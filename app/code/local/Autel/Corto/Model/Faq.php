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
class  Autel_Corto_Model_Faq extends Mage_Core_Model_Abstract
{
    
    protected function _construct()
    {        
        $this->_init('autelcorto/faq');
    }
    
    public function getId() {
        return $this->getEntityId();
    }
    
        
    protected function _beforeSave() {

        parent::_beforeSave();
    }
    
    
}

?>
