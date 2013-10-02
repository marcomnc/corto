<?php

class Autel_Corto_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_catalogModule = array ( 'Catalog' => 1,
                                        'CatalogIndex' => 1,
                                        'CatalogInventory' => 1,
                                        'CatalogRule' => 1,
                                        'CatalogSearch' => 1);
    
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
    
    public function getIsCatalogPage()
    {
        $module = Mage::app()->getFrontController()->getRequest()->getModuleName();
        $catalogPage = false;

        return isset($this->_catalogModule[$module]);
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
    
    /**
     * Recupero la country in base all'IP utilizzando i WS di webservicex.net
     * @param type $ip
     * @param type $debug
     */
    protected function _getGeoIpState($ip = "", $debug = false ) {
        //Verifico se ho in sessione la country
        if ($debug) {
            Mage::Log("ip to check $ip", null, '', true);
            Mage::Log($_SERVER, null, '', true);
        }
        
        if ($ip === true) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        if ($ip != "") {
            $ip = "&ip=$ip";
        }        
        
        $key = Mage::getConfig('autelcorto/store_zone/infodb_key');
        
        if ($key != "") {
        
            try {
                $xml = file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=$key&format=XML$ip");
                
                $xmlLocation = new SimpleXMLElement($xml);
                
                if ($xmlLoaction->statusCode == "OK") {
                    
                    return $xmlLoaction->countryCode;
                    
                }
                
            } catch (Exception $e) {
                Mage::LogException($e);
            }
        }
        return false;
    }
    
    
        public function getCountryFromIp($ip = "") {
        $country = false;
        $stateIso2 = $this->_getGeoIpState($ip,true);
        if ($stateIso !== false) {
            $country = Mage::getModel('directory/country')->Load($stateIso2, 'iso2_code');
        }

        if ($country === false && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            foreach (explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) as $accept) {
                if (preg_match("!([a-z-]+)(;q=([0-9.]+))?!", trim($accept), $found)) {
                    $langs[] = $found[1];
                    $quality[] = (isset($found[3]) ? (float) $found[3] : 1.0);
                    //Mage::log("Found language in request: ".$langs." with quality ".$quality);
                }
            }
            // Order the codes by quality
            array_multisort($quality, SORT_NUMERIC, SORT_DESC, $langs);
            // iterate through languages found in the accept-language header
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
}
?>