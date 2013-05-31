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
 
        $cookie = self::getCookie();
        //$front = $observer->getFront();
        if ((Mage::Registry('_from_wp') !== true && !$cookie->getNoRequest()) || $cookie->getWebsiteId() != Mage::app()->getStore()->getWebSiteId()) {

            if ($cookie->getStore() != "") {

                if (Mage::app()->getStore()->getCode() != $cookie->getStore()) {

                   $cookie->setAction(self::ACTION_WARNING_SELECT); 
                }            
            } else {
                $cookie->setAction(self::ACTION_SELECT); 
            }   
        } else {
            //SE vengo
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
