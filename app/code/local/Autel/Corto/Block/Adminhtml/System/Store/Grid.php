<?php

class Autel_Corto_Block_Adminhtml_System_Store_Grid extends Mage_Adminhtml_Block_System_Store_Grid
{
    
    protected function _prepareColumns()
    {
        $this->addColumn('website_title', array(
            'header'        => Mage::helper('core')->__('Website Name'),
            'align'         =>'left',
            'index'         => 'name',
            'filter_index'  => 'main_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_website'
        ));
        $this->addColumn('website_id', array(
            'header'        => Mage::helper('core')->__('Ws Id'),
            'align'         =>'left',
            'index'         => 'website_id',
            'filter_index'  => 'main_table.website_id',
            'width'         => '30px'
        ));

        $this->addColumn('group_title', array(
            'header'        => Mage::helper('core')->__('Store Name'),
            'align'         =>'left',
            'index'         => 'group_title',
            'filter_index'  => 'group_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_group'
        ));

        $this->addColumn('store_title', array(
            'header'        => Mage::helper('core')->__('Store View Name'),
            'align'         =>'left',
            'index'         => 'store_title',
            'filter_index'  => 'store_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_store'
        ));
        $this->addColumn('store_id', array(
            'header'        => Mage::helper('core')->__('Store Id'),
            'align'         =>'left',
            'index'         => 'store_id',
            'filter_index'  => 'main_table.store_id',
            'width'         => '30px'
        ));

        return parent::_prepareColumns();
    }
    
    
}

