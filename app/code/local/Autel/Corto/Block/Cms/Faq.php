<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Faq
 *
 * @author Doctor
 */

function _sortFaq ($a, $b) {
    $aKey = (!isset($a['order'])  || !is_numeric($a['order']))?0:$a['order'];
    $bKey = (!isset($b['order'])  || !is_numeric($b['order']))?0:$b['order'];

    if ($aKey == $bKey) {
        return 0;
    }
    return ($aKey < $bKey) ? -1 : 1;
}
class Autel_Corto_Block_Cms_Faq extends Mage_Core_Block_Template
{
    protected $_faq = array();
    
    public function _construct() {        
        parent::_construct();
        
        $faqs = unserialize(MAge::getStoreConfig('autelcorto/cms/faq'));       
        
        echo "<pre>";
         print_r($faqs);
        usort($faqs, "_sortFaq");
        print_r($faqs);
        uasort($faqs, "_sortFaq");
         print_r($faqs);
    
    }
    
        
}

?>
