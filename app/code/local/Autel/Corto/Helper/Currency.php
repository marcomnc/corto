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
 * @category    Core
 * @package     PrivateSales   
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */

class Autel_Corto_Helper_Currency extends Autel_Corto_Helper_Data {
    
    protected $_listAvailableCurrency = null;


    /**
     * Controlla se è necessario mostrare l'exchange rate
     * @return bool
     */
    public function isExchangeRateVisible() {
        return (sizeof(Mage::app()->getStore()->getAvailableCurrencyCodes()) > 1) ? true : false ;
    }
    
    /**
     * Ritorna la lista delle valute per cui è necessario mostrare l'exchange rate
     * @return array Codice => Mage_Directory_Model_Currency
     */
    public function getListAvailableCurrency() {
    
        
        if (is_null($this->_listAvailableCurrency)) {
        
            foreach (Mage::app()->getStore()->getAvailableCurrencyCodes() as $code) {

                if ($code != Mage::app()->getStore()->getCurrentCurrencyCode())
                    $this->_listAvailableCurrency[$code] = Mage::getModel('directory/currency')->load($code);

            } 
            
        }
        
        return $this->_listAvailableCurrency;
                
    }
    
    
    public function setCustomCurrencyToView($code = '') {
        Mage::getSingleton('catalog/session')->setCustomCurrencyToView($code);
        Mage::getModel('core/cookie')->set("currency-selector", $code, 60*60*24);
    }
    
    public function getCustomCurrencyToView() {

        $code = Mage::getSingleton('catalog/session')->getCustomCurrencyToView() . '';
        
        if ($code == '') {
            $code = Mage::getModel('core/cookie')->get("currency-selector");
            if ($code == '') {
                $code = Mage::app()->getStore()->getDefaultCurrencyCode();
            }
            Mage::getSingleton('catalog/session')->setCustomCurrencyToView($code);
        }
        
        return $code;
    }
    
}

?>
