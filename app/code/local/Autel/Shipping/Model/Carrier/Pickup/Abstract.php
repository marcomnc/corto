<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class Autel_Shipping_Model_Carrier_Pickup_Abstract extends Mage_Shipping_Model_Carrier_Abstract 
{

        
    /**
     * per corto il pick up è valiso solo se sono in zona europa
     * @param Mage_Shipping_Model_Rate_Request $request
     */
    public function checkAvailableShipCountries(Mage_Shipping_Model_Rate_Request $request) {
        
        $cookie = MpsSistemi_Iplocation_Model_Core_Dispatch::RegistryCountry();
        
        return ($cookie->getZoneId() == 'EU');
        
    }
}