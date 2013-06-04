<?php

class Autel_Corto_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getIsHomePage()
    {
        $page = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $homePage = false;

        if($page =='cms'){
            $cmsSingletonIdentifier = Mage::getSingleton('cms/page')->getIdentifier();
            $homeIdentifier = Mage::app()->getStore()->getConfig('web/default/cms_home_page');
            if($cmsSingletonIdentifier === $homeIdentifier){
                $homePage = true;
            }
        }

        return $homePage;
    }    
    
    /**
     * Recupero lo store partendo da uno stato
     * @param string $state
     * @return Mage_Core_Model_Store Store
     */
    public function getStoreFromState($state) {
        
        foreach (Mage::app()->getStores() as $store) {
            $countries = Mage::getStoreConfig('general/country/allow', $store);
            if (strpos(','.$countries.',', ','.  strtoupper($state).',') !== false) {
                return $store;
            }
        }
        return null;
    }
    
    /**
     * Data una zona recupera lo stato di appartenzenza
     * @param int|string|Autel_Corto_Model_Zone $zone 
     */
    public function getStoreFromZone($zone) {
        $store = $this->getStoreEnabledForZone($zone);
        
        if (sizeof($store) > 0 ) {
            return $store[0];
        } 
        return null;
    }
    
    
    public function getStoreEnabledForZone($zone) {
        $wsId = 0;
        $enabledStore = array();
        
        if ($zone instanceof Autel_Corto_Model_Zone) {
            $myZone = $zone;
            $wsId = $zone->getWebsiteId();
        }elseif(is_int($zone+0)) {
            $myZone = Mage::getModel('autelcorto/zone')->Load($zone);
            if (!is_null($myZone)) {
                $wsId = $myZone->getWebsiteId();
            }            
        } else {
            $myZone = Mage::getModel('autelcorto/zone')->Load($zone, 'zone_code');
            if (!is_null($myZone)) {
                $wsId = $myZone->getWebsiteId();
            }            
        }
        
        if ($wsId > 0) {
            
            $store = mage::getModel('core/store')->getCollection();
            $store->getSelect()
                  ->where('store_id in (?)', explode(',', $myZone->getStoreId()))
                  ->order('sort_order');
            foreach ($store as $s) {
                $enabledStore[] =  $s;
            }
        }
        
        return $enabledStore;
    }
    
    
    /**
     *  Dato un stato recupera la zona di appartenzenza
     * @param type $state
     * @return type
     */
    public function getZoneSelectFromState($state) {
        
        $zone = Mage::getModel('autelcorto/zone')->getCollection();
        $zone->getSelect()
             ->where("instr(concat(',',state_list,','),concat(',','$state',',')) > 0");

        foreach ($zone as $z) {
            return $z;
        }
    }
    
    /**
     * Recupero tutte le zone per la selezione
     * @param type $store
     */
    public function getAllZoneSelect($store=null) {
        $zoneList = array();
        
        foreach (Mage::getModel('autelcorto/zone')->getCollection()->getByGroup()->Sort() as $zone) {
            if (!is_null($store)) {
                $stores = Mage::getModel('core/website')->Load($zone->getWebsiteId)->getStoreCollection();
                foreach ($stores as $ws_store) {
                    $store_code = ($store instanceof Mage_Code_Model_Store)?$store->getCode():$store;
                    if ($ws_store->getCode() != $store_code) {
                        continue;
                    }
                }
            }
            
            $zoneList[$zone->getId()] =  array('description' => $zone->getDescription(), 'group' => $zone->getGroupName());
        }
        return $zoneList;
    }
    
    /**
     * Recupero un array contente la lista degli stati (Codice/Descrizione)
     */
    public function getAllActiveState($store = null) {
        
        $stateList = array();
        
        $webSites = Mage::getModel('core/website')->getCollection();
        $webSites->getSelect()->Order('sort_order');
        
        foreach ($webSites as $webSite) {
         
            $stores = Mage::getModel('core/store')->getCollection();
            $stores->getSelect()->where('website_id = ' . $webSite->getId())
                   ->Order('sort_order');
            foreach ($stores as $s) {
                                
                if (!is_null($store)) {
                    if ($store instanceof Mage_Code_Model_Store) {
                        if ($store->getCode() != $s->getCode()) {
                            continue;
                        }                        
                    } else {
                        if ($store != $s->getCode() && $store != $s->getWebsiteId()) {
                            continue;
                        }
                    }
                }
                
                $states = preg_split('/,/',  Mage::getStoreConfig('general/country/allow', $s));

                foreach ($states as $state) {
                    $stateList[$state] =  Mage::getModel('directory/country')->load($state)->getName();
                }
            }
        }
        
        return $stateList;
    }
    
    public function getNewsLetterUrl() {
        return Mage::getUrl('newsletter/manage/');
    }
    
    /**
     * Verifica se le zone sono coerenti per procedere all'aggiornamento
     * @param type $zoneId
     */
    public function checkZone() {
        $stateWsId = array();
       
        foreach (Mage::getModel('autelcorto/zone')->getCollection() as $z) {
      
            foreach (explode(',', $z->getStateList()) as $state) {
                $stateWsId[$state][$z->getWebsiteId()] = $z->getDescription();
            }            
            
        }
 
        $errorMsg = "";
         
        foreach ($stateWsId as $k=>$v) {
        
            if (sizeof($v) > 1) {
                $country = Mage::getModel('directory/country')->Load($k);
                $errorMsg .= $country->getName() . " presente in:<br>";
                foreach ($v as $ws => $zd) {
                    $errorMsg .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    $errorMsg .= MAge::getModel('core/website')->Load($ws)->getName() . " (Zona . " . $zd . ")<br>";
                }
            }
        
        }
        
        if ($errorMsg != "") {
            Mage::getSingleton('adminhtml/session')->addError($errorMsg);
        }
        
        return $errorMsg == "";
    }
    
    public function refreshZone() {
        
        $stateWsId = array();
       
        foreach (Mage::getModel('autelcorto/zone')->getCollection() as $z) {
            if (isset($stateWsId[$z->getWebsiteId()])) {
                $stateWsId[$z->getWebsiteId()] .= ",";
            }
            $stateWsId[$z->getWebsiteId()] .= $z->getStateList();
        }

        $config = new Mage_Core_Model_Config();
        
        foreach ($stateWsId as $k=>$v) {
            $stateList = implode(array_unique(explode(',', $v)), ',');            
            //Azzero la configurazione per store
            foreach (Mage::app()->getStores() as $store) {
                if ($store->getWebsiteId() == $k) {
                    $config->deleteConfig('general/country/allow', 'store', $store->getId());
                }
            }
            
            $config->saveConfig('general/country/allow', $stateList, 'websites', $k);
            
        }       
    }
}
?>