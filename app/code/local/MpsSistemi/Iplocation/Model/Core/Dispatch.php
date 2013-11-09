<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *  
 *
 * @category    
 * @package     
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */
class MpsSistemi_Iplocation_Model_Core_Dispatch {
    
    const COOKIE_NAME           = 'storeLocator';
    const REGISTER_NAME         = '_mps/location/store_status';
    const ACTION_NO_ACTIONO     = 'NAN';
    const ACTION_WARNING        = 'AW';
    const ACTION_SELECT         = 'AS';
    
    const COOKIE_VERSION        = '2.0';

    public function pre_dispatch($observer) {

        // Sono sull'admin non faccio nessuna considerazione
	if (Mage::app()->getStore()->getId() == 0)
		return $observer;
 
        $cookie = self::getCookie();      
        $front = $observer->getFront()->getRequest()->getParams();         
        
        if (isset($front['___reset_cookie'])) {
            $cookie->unsetData();
            self::setCookie($cookie);
        }
        
        if ((!$cookie->hasData() || !$cookie->hasData('version') || $cookie->getData('version') != self::COOKIE_VERSION) ||
            (isset($front['___ip_test']) && $front['___ip_test'] != "")) { 
            //Tento la geolocalizzazione e creo il cookie di base
            $country = Mage::helper('mpslocation')->getCountryFromIp(((isset($front['___ip_test']) && $front['___ip_test'] != ""))? $front['___ip_test'] : "");
            if ($country !== false && $country instanceof Mage_Directory_Model_Country) {
               
                // PAese Geolocalizzato
                $cookie->setData('country_code', $country->getCountryId());
                $cookie->setData('country_name', $country->getName());
                //Info store default del ws selezionato      
                $myStore = Mage::helper('mpslocation')->getStoreFromState($country->getCountryId());
                if (!is_null($myStore)) {
                    $cookie->setData('store', $myStore->getCode());
                    $cookie->setData('website_id', $myStore->getWebsiteId());
                    $cookie->setData('action', self::ACTION_NO_ACTIONO);
                    
                    $zone = Mage::Helper('mpslocation')->getZoneFromCountry($country);
                    $cookie->setData('zone_id', $zone);
                    $enabledStore = array();
                    foreach (Mage::Helper('mpslocation')->getStoreEnabledForZone($zone) as $enStore) {
                        $enabledStore[] = $enStore->getCode();
                    }
            
                    $cookie->setData('enabled_store', $enabledStore);
                    
                    self::setCookie($cookie);
                }
                
            }
        }
        
        if (isset($front['___please_logme_store_cookie']) && $front['___please_logme_store_cookie'] == "yes") {
            echo "<pre>";
            print_r($cookie);
            die();
        }
        
//echo Mage::app()->getStore()->getWebSiteId() . " - <pre>";
//print_r( $cookie);
//
//die();
//echo "</pre>";
	//Se non sono su wordpress è il mio cookie non corrisponde allo store dell'url e non ho forzato che non devo richiedere
        if ((Mage::Registry('_from_wp') !== true && !$cookie->getNoRequest()) || $cookie->getWebsiteId() != Mage::app()->getStore()->getWebSiteId()) {
		
            //SE ho già uno store impostato 
            if ($cookie->getStore() != "") {
		
		//il mio store non corrisponde con quello dell'url ti reindirizzo al tuo store
                if (Mage::app()->getStore()->getCode() != $cookie->getStore()) {
                    
                    $requestInfo = $observer->getFront()->getRequest()->getOriginalPathInfo();                    
                    $storeFromPath = $observer->getFront()->getRequest()->getStoreCodeFromPath().''; 
                    if ($storeFromPath != '') {
                        $storeFromPath = "/$storeFromPath";
                    }
                    
                    if ($storeFromPath != '' && strpos( $observer->getFront()->getRequest()->get("REQUEST_URI"), "$storeFromPath/") !== 0) {
                        //La prima parte del percoso non è lo store 
                        $storeFromPath = "";
                    }

                    $additionaParameters = preg_replace("/". preg_replace("/\//", "\/", $storeFromPath . $observer->getFront()->getRequest()->getOriginalPathInfo()) ."/", "", $observer->getFront()->getRequest()->get("REQUEST_URI"));

                    //Ricacolo il nome del paese in base allo store
                    $store = Mage::getModel('core/store')->Load($cookie->getStore());
                    
                    //Cerco un eventuale rewrite...
                    
                    $urlRewrite = Mage::GetModel('core/url_rewrite')->getCollection();
                    $urlRewrite->getSelect()                               
                               ->Join(array('rewrite' => Mage::getSingleton('core/resource')->getTableName('core/url_rewrite')),
                                      'main_table.target_path = rewrite.target_path')
                               ->where("main_table.request_path in ( '$requestInfo', '$requestInfo/', '" . substr($requestInfo, 1) . "', '" . substr($requestInfo, 1) . "/')" )
                               ->where("main_table.store_id in (0, " . Mage::app()->getStore()->getId() . ")")
                               ->where("rewrite.store_id = ? ",  $store->getId())
                               ->reset(Zend_Db_Select::COLUMNS)
                               ->columns(array('rewrite.request_path as path'));
                    $newPathInfo = "";
                    foreach ($urlRewrite as $ur) {
                       $newPathInfo = $ur->getPath(); 
                       break;
                    }
                    
                    if ($newPathInfo == "" ) {
                        //Non ho trovato un rewite valido, verifico se è un router standard
                        $modules = preg_split("/\//", $requestInfo);
                        
                        $module = ($modules[0] == "" && isset($modules[1])) ? $modules[1] : "";
                        if (is_array($observer->getFront()->getRouter("standard")->getModuleByFrontName($module))) {
                            //l'url fa capo ad un modulo standard ... provo a cambiare lo store
                            $newPathInfo = $requestInfo;
                        }                                                
                    }
                    
                    $newUrl = $store->getUrl() . $newPathInfo .  (($newPathInfo != "") ? $additionaParameters : "");
                
                    // Forzo la visualizzazione di un warning
                    $cookie->setData('action', self::ACTION_WARNING);
                    self::setCookie($cookie);
                    
                    Header("location: " . $newUrl);
                    die();
                }            
            } else {
		//Non ho uno store impostato, e non ho forzato che non devo fare richieste quandi faccio la richiesta
                $cookie->setAction(self::ACTION_SELECT); 
            }   
        } else {

		//Se lo store  e diverso da quello del cookie (ma appartengono allo stesso WS) ed ho impostato il ___from_store quindi vengo dalla language selector
		if (Mage::app()->getStore()->getCode() != $cookie->getStore() && isset($front['___from_store']))  {
			//Imposto  il nuovo store come store base per le volte successive
			$cookie->setStore(Mage::app()->getStore()->getCode());
			self::setCookie($cookie);
		}
            //SE sono qui dignifica che non ho bisogno di chiederti lo store
            $cookie->setAction(self::ACTION_NO_ACTIONO); 
        }
        self::RegisterCountry($cookie);        
    }
    
    static function getCookie() {
        $ret = new Varien_Object();        
            
        $cookie =  unserialize(base64_decode(Mage::getModel("core/cookie")->get(self::COOKIE_NAME)));

        if (!is_null($cookie) &&  is_array($cookie)) {
            foreach ($cookie as $k=>$v) {
                $ret->setData($k, $v);
            }
        }
        
        return $ret;
    }
    
    static function setCookie($dataToSet, $time = null) {
        
        if ($dataToSet instanceof  Varien_Object) {
            foreach ($dataToSet->getData() as $k => $v) {
                $cookie[$k] = $v;
            }            
        } elseif (is_array ($dataToSet)) {            
            foreach ($dataToSet as $k => $v) {
                $cookie[$k] = $v;
            }            
        } else {
            $cookie = array ("lan" => "");
        }
        
        if (!key_exists('lan', $cookie) || $cookie["lan"] == "") {
            $cookie["lan"] = self::_getLanguage();
        }
        
        $cookie["version"] = self::COOKIE_VERSION;

        if (is_null($time)) {
            $time = 60*60*24*30; // 1 mese
        }
        
        //Registro il cookie
        Mage::getModel("core/cookie")->set(self::COOKIE_NAME, base64_encode(serialize($cookie)), $time);
        //Memorizzo i dati in sessione
        self::RegisterCountry($cookie);
        
    }
         
    public static function RegisterCountry($country) {
        if (Mage::registry(self::REGISTER_NAME) != null) {
            Mage::unregister(self::REGISTER_NAME);
        }
        Mage::register(self::REGISTER_NAME, $country);
    }
    
    public static function RegistryCountry() {
        return Mage::registry(self::REGISTER_NAME);
    }
    
    
    private static function _getLanguage() {

        $langs = Mage::Helper('mpscore')->getLangsFromBrowserAgent();
        
        foreach ($langs as $lang) {
            return $lang;
        }
        
        return "";
     }

}

?>
