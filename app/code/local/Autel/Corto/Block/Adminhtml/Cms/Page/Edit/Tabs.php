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
class Autel_Corto_Block_Adminhtml_Cms_Page_Edit_Tabs extends Mage_Adminhtml_Block_Cms_Page_Edit_Tabs {
    
    protected function _beforeToHtml(){
        
        parent::_beforeToHtml();

        
        $this->addTab('colors', array(
            'label'     => Mage::helper('autelcorto')->__('CORTO - Blocchi Associati'),
            'title'     => Mage::helper('autelcorto')->__('CORTO - Blocchi Associati'),
            'content'   => $this->getLayout()->createBlock('autelcorto/adminhtml_cms_page_edit_tab_blocklist')->toHtml(),
        ));
        
        Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
        
    }
    
    
}

?>
