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
class MpsSistemi_Twitter_Model_Api extends Mage_Core_Model_Abstract {
    
    public function _construct() {
        
        parent::_construct();
    }

    /**
     * Recupera la time line per un utente
     * @param type $users
     */
    public function getTimeLine($user, $count = 0) {
        
        $result = false;
        $helper = Mage::Helper('mpstwitter/api_timeline');
        
        if (($user ?: '') != '') {
            
            if ($count == 0) {
                $count = 20;
            }
            
            $token = $helper->AuthenticateMe();

            if ($token !== false) {

                $result = $helper->execute($user, $count, $token);

            } else {

                Mage::log($helper->__('Errore nel recupero del token'));

            }
        }
        return $result;
        
    }
    
}

?>
