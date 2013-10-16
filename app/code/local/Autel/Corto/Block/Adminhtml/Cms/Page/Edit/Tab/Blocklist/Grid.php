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
class Autel_Corto_Block_Adminhtml_Cms_Page_Edit_Tab_Blocklist_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct()
    {        
        parent::__construct();
        $this->setId('blocklist_grid');
        $this->setDefaultSort('block_id');
        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
        if ($this->_getPage()->getId()) {
            $this->setDefaultFilter(array('in_page'=>1));
        }
    }

    
    public function getGridUrl()
    {
        return Mage::helper('adminhtml')->getUrl('autelcorto/adminhtml_cms_block/blocklist', array('_current'=>true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cms/block')->getCollection();
        /* @var $collection Mage_Cms_Model_Mysql4_Block_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('in_page', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_page',
            'values'    => $this->_getSelectedBlocks(),
            'align'     => 'center',
            'index'     => 'block_id',
            'renderer'  => 'autelcorto/adminhtml_widget_grid_column_renderer_submit_checkbox'
        ));
        
        $this->addColumn('block_title', array(
            'header'    => Mage::helper('cms')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('block_identifier', array(
            'header'    => Mage::helper('cms')->__('Identifier'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));


        $this->addColumn('block_is_active', array(
            'header'    => Mage::helper('cms')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('cms')->__('Disabled'),
                1 => Mage::helper('cms')->__('Enabled')
            ),
        ));
        
        $this->addColumn('block_position', array(
            'header'    => Mage::helper('autelcorto')->__('Position'),
            'name'      => 'position',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'position',
            'width'     => '1',
            'editable'  => true,
            'edit_only' => !$this->_getPage()->getId(),
            'renderer'  => 'autelcorto/adminhtml_widget_grid_column_renderer_submit_input'
        ));
    }
    
    private function _getPage() {
        return Mage::registry('cms_page');
    }
    
    private function _getSelectedBlocks() {
        return array();
    }
}

?>
