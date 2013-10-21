<?php

/**
 * Description of LocationController
 *
 * @author marcoma
 */
class Autel_Corto_LocationController extends Mage_Core_Controller_Front_Action
{
    public function selectAction() {
        
        $action = $this->getRequest()->getParam('action')."";
        $country = $this->getRequest()->getParam('countryCode')."";
        $urlref = $this->getRequest()->getParam('urlref')."";
        
        $block = $this->getLayout()->createBlock('core/template');
        
        if ($action == Autel_Corto_Model_Core_Dispatch::ACTION_WARNING || $action == Autel_Corto_Model_Core_Dispatch::ACTION_WARNING_SELECT) {
            $block->setWarning(true);
        }
        if ($urlref != "") {
            $block->setReferrer($urlref);
        }

        foreach (Mage::getModel('directory/country')->getCollection() as $mageCountry) {            
            if ($mageCountry->getIso3Code() == $country) {
                //Me lo porto sempre dietro
                $block->setCountryCode($mageCountry->getCountryId());
                $block->setCurrent(Mage::Helper('autelcorto')->getZoneSelectFromState($mageCountry->getCountryId()));
                break;
            }
        }

        $block->setCountryList(Mage::Helper('autelcorto')->getAllZoneSelect());
        
        
        $block->setTemplate('corto/location.phtml');
        
        $this->getResponse()->setBody($block->toHtml());
        
    }
    
    public function setAction() {
        $ret = array("action"   => "close",
                     "url"      => "");
        $country = $this->getRequest()->getParam('countrycode');
        $zone =  $this->getRequest()->getParam('zone');
        $isHome = $this->getRequest()->getParam('home');
        $urlref = $this->getRequest()->getParam('urlref')."";
        if ($urlref != "") {
            $urlref = base64_decode($urlref);
        }
        $cookie = Mage::Registry(Autel_Corto_Model_Core_Dispatch::REGISTER_NAME);
        
        $cookie->setData('no_request', true);
        
        if ($zone == '' || $zone == 'none') {
            //Voglio continuare senza stato impostato                        
            Autel_Corto_Model_Core_Dispatch::setCookie($cookie, 60*60*24); //x Un giorno
        } else {
            
            // PAese Geolocalizzato
            $cookie->setData('country_code', $country);
            $cookie->setData('country_name', Mage::getModel('directory/country')->load($country)->getName());
            //Info store default del ws selezionato      
            $myStore =  Mage::Helper('autelcorto')->getStoreFromZone($zone);
            $cookie->setData('store', $myStore->getCode());
            $cookie->setData('website_id', $myStore->getWebsiteId());
            $cookie->setData('zone_id', $zone);
            
            $enabledStore = array();
            foreach (Mage::Helper('autelcorto')->getStoreEnabledForZone($zone) as $enStore) {
                $enabledStore[] = $enStore->getCode();
            }
            
            $cookie->setData('enabled_store', $enabledStore);            

            // Imposta anche il register
            Autel_Corto_Model_Core_Dispatch::setCookie($cookie); 

            if (Mage::app()->getStore()->getCode() != $myStore->getCode()) {
                if ($isHome == "1") {
                    // Sono alla home è lo store è diverso... lo cambio
                    $ret = array("action" => "redirect",
                                 "url"    => Mage::getModel('core/url')->getUrl('', array('_store' => $myStore->getId())));
                } else {
                    if ($urlref != "") {
                        $z = $urlref;
                        $urlref = str_replace(Mage::getModel('core/url')->getUrl(), '', $urlref);                        
                        $ret = array("action" => "redirect",
                                     "url"    => $myStore->getUrl($urlref),
                                     "z" => $urlref,
                                     'k' => Mage::getModel('core/url')->getUrl());
                    } else {
                        $ret = array("action" => "reload",
                                     "url"    => '');
                    }
                }
            } else {
                $ret = array("action" => "redirect",
                             "url"    => $urlref,);
            }
                    
        }
        
        $this->getResponse()->setBody(MAge::Helper('core')->jsonEncode($ret));
                
    }
    
    /**
     * Imposto in sesione la valuta con cui voglio visualizzare l'importo. richiesta ajax non ha effetto
     */
    public function setcustomcurrencyviewAction() {
        $currency = preg_split("/-/", $this->getRequest()->getParam('code',''));       
        if (is_array($currency) && isset($currency[2])) {
            foreach (Mage::app()->getStore()->getAvailableCurrencyCodes() as $code) {
                if ($currency[2] == $code) {
                    Mage::helper('autelcorto/currency')->setCustomCurrencyToView($code);
                    die();
                }
            }
        }
    }
}

?>
