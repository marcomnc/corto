<?php


/**
 * Gestione custom dei pesi
 *
 * @author marcoma
 */
class Autel_Corto_Model_Core_Dispatch {
    
    const COOKIE_NAME           = 'storeLocator';
    const REGISTER_NAME         = '_autel/corto/store_status';
    const ACTION_NO_ACTIONO     = 'NAN';
    const ACTION_WARNING_SELECT = 'AWS';
    const ACTION_WARNING        = 'AW';
    const ACTION_SELECT         = 'AS';

    public function pre_dispatch($observer) {

	if (Mage::app()->getStore()->getId() == 0)
		return $observer;
 
        $cookie = self::getCookie();
        $front = $observer->getFront()->getRequest()->getParams();
        
        if (!$cookie->hasData()) {
            //Tento la geolocalizzazione e creo il cookie di base
            $country = Mage::helper('autelcorto')->getCountryFromIp();
            if ($country !== false) {
                // PAese Geolocalizzato
                $cookie->setData('country_code', $country);
                $cookie->setData('country_name', Mage::getModel('directory/country')->load($country)->getName());
                //Info store default del ws selezionato      
                $myStore = Mage::helper('autelcorto')->getStoreFromState($country);
                $cookie->setData('store', $myStore->getCode());
                $cookie->setData('website_id', $myStore->getWebsiteId());
            
                self::setCookie($cookie);
                
            }
        }
        
//echo Mage::app()->getStore()->getWebSiteId() . " - <pre>";
//print_r( $observer->getFront()->getRequest());
//
//die();
//echo "</pre>";
	//Se non sono su wordpress è il mio cookie non corrisponde allo store dell'url e non ho forzato che non devo richiedere
        if ((Mage::Registry('_from_wp') !== true && !$cookie->getNoRequest()) || $cookie->getWebsiteId() != Mage::app()->getStore()->getWebSiteId()) {
		
            //SE ho già uno store importato 
            if ($cookie->getStore() != "") {
		
		//il mio store non corrisponde con quello dell'url ti reindirizzo alla home del tuo store
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
                        if (is_array($observer->getFront()->getRouter("standard")->getModuleByFrontName($module     ))) {
                            //l'url fa capo ad un modulo standard ... provo a cambiare lo store
                            $newPathInfo = $requestInfo;
                        }                                                
                    }
                    
                    $newUrl = $store->getUrl() . $newPathInfo .  (($newPathInfo != "") ? $additionaParameters : "");
                
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
            
            foreach ($langs as $lang) {
                return $lang;
            }
        }
        return "";
     }

}

?>
