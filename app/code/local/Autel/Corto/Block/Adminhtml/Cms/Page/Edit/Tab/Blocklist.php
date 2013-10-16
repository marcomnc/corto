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

class Autel_Corto_Block_Adminhtml_Cms_Page_Edit_Tab_Blocklist extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    
    protected function _prepareForm()
    {
        $block = $this->getLayout()->createBlock('autelcorto/adminhtml_cms_page_edit_tab_blocklist_grid',
                'autelcorto.adminhtml.cms.page.edit.tab.blocklist.grid');
        
        $this->setChild('blocks.grid', $block );
        
        $blockSerialize = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer',
                'autel.corto.blocks.serialize');
        $blockSerialize->initSerializerBlock('autelcorto.adminhtml.cms.page.edit.tab.blocklist.grid', 
                                             'getBlocksList', 'block_list', 'block_list');
        $blockSerialize->addColumnInputName(array('position', 'fill', 'width', 'height', 'style', 'class', 'in_page'));
        $this->setChild('blocks.serialized', $blockSerialize );
        
        return parent::_prepareForm();
    }
    
    protected function _toHtml() {        
        ob_start();
        include __DIR__ .'/blocklist.phtml';
        $html = ob_get_clean();
        return $html;
    }
    
    protected function getBlockGridHtml() {        
        return $this->getChildHtml('blocks.grid');
    }
    
    protected function getBlockGridSerializeHtml() {        
        return $this->getChildHtml('blocks.serialized');
    }
    
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel() {
        
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle() {
        
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}

?>
