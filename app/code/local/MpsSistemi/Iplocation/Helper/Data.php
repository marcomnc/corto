<?php

class MpsSistemi_Iplocation_Helper_Data extends MpsSistemi_Core_Helper_Data {

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
     * @param int|string|MpsSistemi_Iplocation_Model_Zone $zone 
     */
    public function getStoreFromZone($zone) {
        $store = $this->getStoreEnabledForZone($zone);
        
        if (sizeof($store) > 0 ) {
            return $store[0];
        } 
        return null;
    }
    
    /**
     * Data una country recupera la zona corrispondente
     * @param Mage_Direcotry_Model_Country|string $country
     */
    public function getZoneFromCountry($country) {
        
        if (is_string($country)) {
            $country = Mage::getModel('directory/country')->Load($country);
        }
        $zone = null;
        if ($country instanceof Mage_Directory_Model_Country && $country->getId() != "") {
            $select = Mage::getModel('mpslocation/zone')->getCollection()
                        ->getByGroup()
                        ->sort();
            $select->getSelect()                   
                   ->where("CONCAT(  ',', state_list,  ',' ) LIKE  '%," . $country->getCountryId() .",%'")
                   ->order();
            foreach ($select as $z) {
                $zone = $z->getZoneCode();
                break;
            }
        }
        
        return $zone;
    }
    
    public function getStoreEnableForCountry($country) {
        $zone = $this->getZoneFromCountry($country);
        $enabledStore = array();
        if (!is_null($zone)) {
            $enabledStore = $this->getStoreEnabledForZone($zone);
        }
        return $enabledStore;
    }
    
    
    public function getStoreEnabledForZone($zone) {
        $wsId = 0;
        $enabledStore = array();
    
        if ($zone instanceof MpsSistemi_Iplocation_Model_Zone) {
            $myZone = $zone;
            $wsId = $zone->getWebsiteId();
        }elseif((int)$zone) {
            $myZone = Mage::getModel('mpslocation/zone')->Load((int)$zone);
            if (!is_null($myZone)) {
                $wsId = $myZone->getWebsiteId();
            }            
        } else {
            $myZone = Mage::getModel('mpslocation/zone')->Load($zone, 'zone_code');
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
        
        $zone = Mage::getModel('mpslocation/zone')->getCollection();
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
        
        foreach (Mage::getModel('mpslocation/zone')->getCollection()->getByGroup()->Sort() as $zone) {
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
        
    /**
     * Verifica se le zone sono coerenti per procedere all'aggiornamento
     * @param type $zoneId
     */
    public function checkZone() {
        $stateWsId = array();
       
        foreach (Mage::getModel('mpslocation/zone')->getCollection() as $z) {
      
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
       
        foreach (Mage::getModel('mpslocation/zone')->getCollection() as $z) {
            if (isset($stateWsId[$z->getWebsiteId()])) {
                $stateWsId[$z->getWebsiteId()] .= ",";
            }
            $stateWsId[$z->getWebsiteId()] .= $z->getStateList();
        }

        $config = new Mage_Core_Model_Config();
        
        foreach ($stateWsId as $k=>$v) {
            $allowBillingCountry = Mage::app()->getWebsite($k)->getConfig('mpslocation_options/store_zone/allow_billing_country');
            $stateList = implode(array_unique(explode(',', $v)), ',');            
            
            $this->_resetStoreCountry($k);
            
            if (!$allowBillingCountry) {
                //Imposto abilitati tutti i paesi e blocco le spedizioni
                $allowCountry = implode(Mage::getModel('directory/country')->getResourceCollection()->toArray());
                $config->saveConfig('general/country/allow', $allowCountry, 'websites', $k);
                
                foreach ( Mage::app()->getWebsite($k)->getConfig('carriers') as $code => $cconfig) {
                    $config->saveConfig("carriers/$code/specificcountry", $stateList , 'websites', $k);
                }
            } else {            
                //Imposto solo paesi abilitati. le spedizioni son valide per tutti
                $config->saveConfig('general/country/allow', $stateList, 'websites', $k);
            }
            
        }       
    }
    
    /**
     * Recupero la country in base all'IP utilizzando i WS di infodb
     * @param type $ip
     * @param type $debug
     */
    protected function _getGeoIpState($ip = "", $debug = false ) {
        //Verifico se ho in sessione la country
        if ($debug) {
            Mage::Log("ip to check $ip", null, '', true);
            Mage::Log($_SERVER, null, '', true);
        }
        
        if ($ip == "" && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
       
        if ($ip != "") {
            $ip = "&ip=$ip";
        }        

        $key = Mage::getStoreConfig('mpslocation_options/store_zone/infodb_key');
        
        if ($key != "") {
            try {

                $xml = file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=$key&format=xml$ip");
              
                $xmlLocation = new SimpleXMLElement($xml);

                if ($xmlLocation->statusCode == "OK") {
                               
                    return $xmlLocation->countryCode;
                    
                }
                
            } catch (Exception $e) {
                Mage::LogException($e);
                return false;
            }
        }
        return false;
    }
    
    
        public function getCountryFromIp($ip = "") {
        $country = false;
        $stateIso2 = $this->_getGeoIpState($ip);
        if ($stateIso2 !== false) {
            $country = Mage::getModel('directory/country')->Load($stateIso2);
        }

        if ($country === false && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            
            $langs = $this->getLangsFromBrowserAgent();
            
            foreach ($langs as $lang) {
                $lang = substr($lang,0,2);
                foreach (Mage::getModel('core/store')->getCollection() as $_store) {
                    if ((strtolower(Mage::getStoreConfig('general/country/default', $_store)) == strtolower($lang) ||
                        ( strtolower($lang) == "en" && strtolower(Mage::getStoreConfig('general/country/default', $_store)) == 'gb' )) &&
                        $_store->getIsActive()) {
                            $country = Mage::getModel('directory/country')->Load(Mage::getStoreConfig('general/country/default', $_store), 'country_id');
                            return $country;
                    }
                }
            }
        }

        return $country;
    }
    
   
    /**
     * Resetta eventuali configurazione dei permessi delle country per store e dei metodi di spedizione abilitati per store
     */
    private function _resetStoreCountry($webSiteId) {
        
        $config = new Mage_Core_Model_Config();
        
        //Azzero la configurazione per store
        foreach (Mage::app()->getStores() as $store) {
            if ($store->getWebsiteId() == $webSiteId) {
                $config->deleteConfig('general/country/allow', 'store', $store->getId());
            }
            
            foreach (Mage::getStoreConfig('carriers', $store) as $code => $cconfig) {
                if (Mage::getStoreConfigFlag("carriers/$code/active", $store->getId())) { //Lavoro solo su vettori attivi
                    $config->deleteConfig("carriers/$code/specificcountry", 'store', $store->getId());
                }                                
            }            
        }
    }
    
}
?>