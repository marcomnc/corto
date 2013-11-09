<?php

class MpsSistemi_Core_Helper_Data extends Mage_Core_Helper_Abstract {

    
    public function getLangsFromBrowserAgent() {
        $langs = array();
        
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            foreach (explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) as $accept) {
                $found = null;
                if   (preg_match("!([a-z-]+)(;q=([0-9\\.]+))?!", trim($accept), $found)) {
                    $langs[] = $found[1];
                    $quality[] = (isset($found[3]) ? (float) $found[3] : 1.0);
                }
            }
            // Order the codes by quality
            array_multisort($quality, SORT_NUMERIC, SORT_DESC, $langs);                        
        }
        return $langs;
    }
    
    public function getIsHomePage() {
        return $this->getIsSpecifyCMSPage(Mage::app()->getStore()->getConfig('web/default/cms_home_page'));
    }
    
    public function getIsSpecifyCMSPage($idPage)
    {
        $page = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $specPage = false;

        if($page =='cms'){
            $cmsSingletonIdentifier = Mage::getSingleton('cms/page')->getIdentifier();
            if($cmsSingletonIdentifier == $idPage){
                $specPage = true;
            }
        }

        return $specPage;
    }  
    
}
?>