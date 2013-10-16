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


class Autel_Corto_Adminhtml_Cms_BlockController extends Mage_Adminhtml_Controller_Action{
    
    public function blocklistAction() {
        //$this->_initProduct();
        $page = Mage::getModel('cms/page')->Load($this->getRequest()->getParam('page_id'));
        
        Mage::unregister('cams_page');
        Mage::register('cms_page', $page);        
        $this->loadLayout();                
        $this->getResponse()->setBody($this->getLayout()->createBlock('autelcorto/adminhtml_cms_page_edit_tab_blocklist_grid')->toHtml());
        
    }
    
}

?>
