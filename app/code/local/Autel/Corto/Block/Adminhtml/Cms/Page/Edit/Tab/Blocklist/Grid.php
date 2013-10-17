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
        $pageId = $this->_getPage()->getId();
        if ($pageId)
            $collection->getSelect()
                       ->JoinLeft(array('_rel' => Mage::getSingleton('core/resource')->getTableName('autelcorto/cms_pageblocks')),
                                  "_rel.block_id = main_table.block_id and _rel.page_id = $pageId",
                                  array('_rel.position', '_rel.fill', '_rel.width', '_rel.height', '_rel.style', '_rel.class'));

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
            'width'     => '120',
            'options'   => array(
                0 => Mage::helper('cms')->__('Disabled'),
                1 => Mage::helper('cms')->__('Enabled')
            ),
        ));
        
        $this->addColumn('position', array(
            'header'    => Mage::helper('autelcorto')->__('Position'),
            'name'      => 'position',
            'validate_class' => 'validate-number',
            'index'     => 'position',        
            'editable'  => true,
            'edit_only' => true,
            'renderer'  => 'autelcorto/adminhtml_widget_grid_column_renderer_input'
        ));
        $this->addColumn('fill', array(
            'header_css_class' => 'a-center',
            'type'      => 'select',
            'header'    => Mage::helper('autelcorto')->__('Fill'),
            'options'   => array(
                0 => Mage::helper('autelcorto')->__('No'),
                1 => Mage::helper('autelcorto')->__('Si'),
                2 => Mage::helper('autelcorto')->__('Page')
            ),
            'name'      => 'fill',
            'index'     => 'fill',            
        ));
        $this->addColumn('width', array(
            'header'    => Mage::helper('autelcorto')->__('Width'),
            'name'      => 'width',
            'validate_class' => 'validate-number',
            'index'     => 'width',
            'editable'  => true,
            'edit_only' => true,
            'renderer'  => 'autelcorto/adminhtml_widget_grid_column_renderer_input'
        ));
        $this->addColumn('height', array(
            'header'    => Mage::helper('autelcorto')->__('Height'),
            'name'      => 'height',
            'validate_class' => 'validate-number',
            'index'     => 'height',
            'editable'  => true,
            'edit_only' => true,
            'renderer'  => 'autelcorto/adminhtml_widget_grid_column_renderer_input'
        ));
        $this->addColumn('style', array(
            'header'    => Mage::helper('autelcorto')->__('Style'),
            'name'      => 'style',
            'index'     => 'style',
            'width'     => '200',
            'editable'  => true,
            'edit_only' => true,
            'renderer'  => 'autelcorto/adminhtml_widget_grid_column_renderer_input'
        ));
        $this->addColumn('class', array(
            'header'    => Mage::helper('autelcorto')->__('Class'),
            'name'      => 'class',
            'index'     => 'class',
            'width'     => '200',
            'editable'  => true,
            'edit_only' => true,
            'renderer'  => 'autelcorto/adminhtml_widget_grid_column_renderer_input'
        ));
    }
    
    private function _getPage() {
        return Mage::registry('cms_page');
    }
    
    private function _getSelectedBlocks() {
        if ($this->_getPage())
            return Mage::getModel('autelcorto/cms_pageblocks')->getSelectBlockForPageArray($this->_getPage()->getId());
        else 
            return array();
    }
    
    private function _getSelectedFills() {
        
    }
    
    /**
     * Callback che recupera i valori
     */
    public function getBlocksList() {

        $blockList = array();
        $page = $this->_getPage();
       
        if ($page && $page->getId() > 0 ){
            
            $lists = Mage::getModel('autelcorto/cms_pageblocks')
                    ->getCollection()
                    ->addPageFilter($page->getId());
            foreach ($lists as $list) {
                $blockList[$list->getBlockId()] = array(
                    'position'  => $list->getPosition(),
                    'fill'      => $list->getFill(),
                    'width'     => $list->getWidth(),
                    'height'    => $list->getHeight(),
                    'style'     => $list->getStyle(),
                    'class'     => $list->getClass(),                    
                );
            }
        }
        
        return $blockList;
        
    }
}

?>
