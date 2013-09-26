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
class MpsSistemi_Twitter_Block_Sidebar_Timeline extends Mage_Core_Block_Template {
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mpssistemi/twitter/sidebar/timeline.phtml');
    }
    
    public function getTimeLine() {
        
        return Mage::getModel('mpstwitter/api')->getTimeLine(Mage::getStoreConfig('mpstwitter/api_time_line/user'), Mage::getStoreConfig('mpstwitter/api_time_line/count'));
    }
    
    public function getUserName() {
        return Mage::getStoreConfig('mpstwitter/api_time_line/user');
    }
    
    public function getRelativeTime($time) {
        
        return Mage::Helper('mpstwitter')->toRelativeTime($time);
        
    }
}

?>
