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
class MpsSistemi_Twitter_Helper_Data extends Mage_Core_Helper_Abstract {
    
    const TOKEN_TYPE_IDX = 'token_type';
    const ACCESS_TOKEN_IDX = 'access_token';
    
    protected $_oAuthConsumerKey = null;
    protected $_oAuthConsumerSecret = null;
    protected $_oAuthUrl = null;
    protected $_timeOut = null;
    
    protected $_apiUrl = "";
    
    public function __construct() {
        $this->_oAuthConsumerKey = Mage::getStoreConfig('mpstwitter/key/consumer_key');
        $this->_oAuthConsumerSecret = Mage::getStoreConfig('mpstwitter/key/consumer_secret');
        $this->_oAuthUrl = Mage::getStoreConfig('mpstwitter/url/token');
        $this->_timeOut = Mage::getStoreConfig('mpstwitter/url/timeout');
        
        if (!is_numeric($this->_timeOut) || $this->_timeOut == 0) {
            $this->_timeOut = 5000;
        }
        
    }
    
    /**
     * Autentica la sessione twitter recuperando un token
     * @return array TokenType, AccessToken
     */
    public function AuthenticateMe() {
        
        if (($this->_oAuthConsumerKey ?: '') == '' || ($this->_oAuthConsumerSecret ?: '') == '' || ($this->_oAuthUrl ?: '') == '') {
            throw  new Mage_Core_Exception($this->__('Invalid twitter secure parametr configuration'));
        }
        
        $authHeader = "Basic " . base64_encode(urlencode($this->_oAuthConsumerKey) . ":" . urlencode($this->_oAuthConsumerSecret));
        $postBody = array("grant_type"=>"client_credentials");
        
        
        $httpHeader = array("Content-Type:  application/x-www-form-urlencoded;charset=UTF-8",
                            "Authorization: " . $authHeader, 
                            "User-Agent: My Twitter App v1.1",
                            "Accept-Encoding: gzip");
        
        
        return Mage::Helper('core')->jsonDecode($this->_post($this->_oAuthUrl, $httpHeader, true, $postBody ));        
        
    }
    
    public function toRelativeTime($time) 
    {
        $timeArray = explode(" ", $time);
        $hDiff = (time() - strtotime ("$timeArray[2] $timeArray[1] $timeArray[5] $timeArray[3]"));

        if ($hDiff < 60) {
                return $this->__("less than a minute ago");
        } else if($hDiff < 120) {
            return $this->__('about a minute ago');
        } else if($hDiff < (60*60)) {
            return floor($hDiff / 60) . $this->__(' minutes ago');
        } else if($hDiff < (120*60)) {
            return $this->__('about an hour ago');
        } else if($hDiff < (24*60*60)) {
            return $this->__('about ') . (floor($hDiff / 3600)) . $this->__(' hours ago');
        } else if($hDiff < (48*60*60)) {
            return $this->__('1 day ago');
        } else {
            return (floor($hDiff / 86400)). $this->__(' days ago');
        }
                
    }
    
    protected function _post($url, $httpHeader, $post = false, $params = Array() ) {
        
        $result = false;
        
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader); 		
            if ($post) {
                curl_setopt($ch, CURLOPT_POST, 1);
                $postParams = "";
                foreach ($params as $paramKey => $paramValue) {
                    if ($postParams != "") {
                        $postParams .= "&";
                    }
                    $postParams .= "$paramKey=$paramValue";
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);

            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_timeOut / 1000);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeOut / 1000);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute post 
            $result = curl_exec($ch);
            if (curl_errno($ch)) { 
                if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                    Mage::Log($this->__("Timeout in fase di recupero del token. " . curl_errno($ch) .": " . curl_error($ch)));
                } else {
                    Mage::Log($this->__("Errore in fase di recupero del token. " . curl_errno($ch) .": " . curl_error($ch)));
                }

                return false;

            } else { 
                //close connection 
                curl_close($ch);	
            } 
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $result;
    }
            
}

?>
