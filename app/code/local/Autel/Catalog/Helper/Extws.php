<?php

/**
 * Helper generico per wrappare ws esterni
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

/**
 * Parametro per la geolocalizzazione
 */
class myIp {       
    /**     
     * @var string Indirizzo ip da geolocalizzare
     */
    public $IPAddress = "";    
}

class Autel_Catalog_Helper_Extws extends Mage_Core_Helper_Data {
   
    public function getGeoIpState($ip = "", $debug = false) {
        //Verifico se ho in sessione la country
        if ($debug) {
            Mage::Log(Mage::getSingleton("customer/session")->getData());
            Mage::Log($_SERVER);
        }
        $_myCountry = Mage::getSingleton("customer/session")->getGeoCountry()."";
        if ($_myCountry  == "") {
            if ($ip == "") {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
            if ($ip != "") {
                try {
                    $ms = new SoapClient('http://www.webservicex.net/geoipservice.asmx?WSDL');
                    $myIp = new myIp();
                    $myIp->IPAddress = $ip;
                    $wsCountry = $ms->GetGeoIP($myIp);
                    if ($debug) {
                        Mage::Log($myIp);
                        Mage::Log($wsCountry);
                    }
                    if ($wsCountry->GetGeoIPResult->ReturnCodeDetails == "Success") {
                            if ($wsCountry->GetGeoIPResult->CountryName != "Reserved") {
                                $_myCountry	= $wsCountry->GetGeoIPResult->CountryName;
                                Mage::getSingleton("customer/session")->setGeoCountry($_myCountry);
                            }
                    }
                } catch (Exception $e) {
                    Mage::LogException($e);
                }
            }
        }
    
    }
}

?>
