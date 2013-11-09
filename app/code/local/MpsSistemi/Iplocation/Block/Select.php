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
class MpsSistemi_Iplocation_Block_Select extends Mage_Core_Block_Template {

    protected $_inCaseOfWarning = "";
    protected $_inCaseOfError = "";
    
    protected $_warningTemplate = "mps/location/select/warning.phtml";
    protected $_selectTemplate =  "mps/location/select/select.phtml";                                  
    
    public function _construct() {
        
        $this->_inCaseOfWarning = Mage::getStoreConfig('mpslocation_options/location_template/show_in_case_warning');
        $this->_inCaseOfError = Mage::getStoreConfig('mpslocation_options/location_template/show_in_case_error');
        
        $warningTemplate = Mage::getStoreConfig('mpslocation_options/location_template/warning_template');
        $selectTemplate = Mage::getStoreConfig('mpslocation_options/location_template/error_template');
        
        parent::_construct();
        
        $cookie = Mage::Registry(MpsSistemi_Iplocation_Model_Core_Dispatch::REGISTER_NAME);
        switch ($cookie->getAction()) {
            case MpsSistemi_Iplocation_Model_Core_Dispatch::ACTION_WARNING:

                switch ($this->_inCaseOfWarning) {
                    case MpsSistemi_Iplocation_Model_Adminhtml_System_Config_Source_ViewType::WARNING:

                        $this->setTemplate(($warningTemplate) ? $warningTemplate : $this->_warningTemplate);

                        break;
    
                    case MpsSistemi_Iplocation_Model_Adminhtml_System_Config_Source_ViewType::ERROR:

                        $this->setTemplate(($selectTemplate) ? $selectTemplate : $this->_selectTemplate);

                        break;

                    default:
                        break;
                }                    

                break;
            
            case MpsSistemi_Iplocation_Model_Core_Dispatch::ACTION_SELECT:

                switch ($this->_inCaseOfError) {
                    case MpsSistemi_Iplocation_Model_Adminhtml_System_Config_Source_ViewType::WARNING:

                        $this->setTemplate(($warningTemplate) ? $warningTemplate : $this->_warningTemplate);

                        break;
    
                    case MpsSistemi_Iplocation_Model_Adminhtml_System_Config_Source_ViewType::ERROR:

                        $this->setTemplate(($selectTemplate) ? $selectTemplate : $this->_selectTemplate);

                        break;

                    default:
                        break;
                }                    
                        
                break;
            default:
                $this->setTemplate('');
                break;
        }
        
        
    }
            
    
}