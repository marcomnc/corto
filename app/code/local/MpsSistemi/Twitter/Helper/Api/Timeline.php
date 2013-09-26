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
class MpsSistemi_Twitter_Helper_Api_Timeline extends MpsSistemi_Twitter_Helper_Data {
    
    public function __construct() {
        parent::__construct();
        $this->_apiUrl = Mage::getStoreConfig('mpstwitter/api_time_line/url');
    }
    
    /**
     * Recupera la time line di twiteer
     * @param string $user
     * @param int $count 
     * @param array $oAuthToken
     */
    public function execute($user, $count, $oAuthToken) {

        $return = array();

        if ($user != "" && isset($oAuthToken[MpsSistemi_Twitter_Helper_Data::TOKEN_TYPE_IDX]) && isset($oAuthToken[MpsSistemi_Twitter_Helper_Data::ACCESS_TOKEN_IDX])) {
            
            $url = $this->_apiUrl;
            $url .= "?screen_name=$user";
            $url .= "&amp;include_rts=" . Mage::getStoreConfig('pstwitter/api_time_line/rts');
            $url .= "&amp;exclude_replies=" . Mage::getStoreConfig('pstwitter/api_time_line/replay');
            $url .= "&amp;count=$count";
            
            $httpHeader = array("Authorization: " . $oAuthToken[MpsSistemi_Twitter_Helper_Data::TOKEN_TYPE_IDX] . " " . $oAuthToken[MpsSistemi_Twitter_Helper_Data::ACCESS_TOKEN_IDX]);

            $return = $this->_post($url, $httpHeader);

            $return = Mage::Helper('core')->jsonDecode($return);
            
        }

        
        return $return;
        
    }
}

?>
