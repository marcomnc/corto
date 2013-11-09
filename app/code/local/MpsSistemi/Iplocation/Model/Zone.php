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

class  MpsSistemi_Iplocation_Model_Zone extends Mage_Core_Model_Abstract
{
    
    protected function _construct()
    {        
        $this->_init('mpslocation/zone');
    }
    
    public function getId() {
        return $this->getEntityId();
    }
    
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }
        return $this->_getData('store_labels');
    }
    
    public function getStoreLabel($returnDescription=false) {
        $storeId = Mage::app()->getStore()->getId();
        if ($this->hasStoreLabels()) {
            $labels = $this->_getData('store_labels');
            if (isset($labels[$storeId])) {
                return $labels[$storeId];
            } elseif ($labels[0]) {
                return $labels[0];
            } elseif ($returnDescription) {
                return $this->getData('description');
            } else {
                return false;
            }
        } elseif (!isset($this->_labels[$storeId])) {
            $this->_labels[$storeId] = $this->_getResource()->getStoreLabel($this->getId(), $storeId);
        }
        return $this->_labels[$storeId];        
    }
   
    protected function _beforeSave() {

        $zoneList = "";
        if (is_array($this->getStateList())) {
            foreach ($this->getStateList() as $state) {
                if ($zoneList == "") {
                    $zoneList = $state;
                } else {
                    $zoneList .= "," . $state;
                }
            }
            $this->setStateList($zoneList);
        } 
        
        $storeList = $this->getStoreId();
        if (is_array($this->getStoreId())) {
            $storeList = implode($this->getStoreId(),',');
            $this->setStoreId($storeList);
        }
        
        $ws = $this->getWebsiteId();
        $this->setWebsiteId($ws[0]);
        
        parent::_beforeSave();
    }
}

?>
