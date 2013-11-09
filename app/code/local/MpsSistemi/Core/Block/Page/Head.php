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

class MpsSistemi_Core_Block_Page_Head extends Mage_Page_Block_Html_Head {
    
    /**
     * Aggiunge un item al tag head solo si sei verifica una condizione di cofigurazoine
     * @param type $type
     * @param type $name
     * @param type $condConfig
     * @param type $params
     * @param type $if
     * @param type $cond
     */
    public function addItemIfConfig($type, $name, $condConfig, $params = null, $if = null, $cond = null) {
        
        if (Mage::getStoreConfig($condConfig)) {        
            parent::addItem($type, $name, $params, $if, $cond);
        }
    }
        
}
