<?php

class Autel_Corto_Block_Adminhtml_Cms_Faq_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('entity_id');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {           
        $collection = Mage::getModel('autelcorto/faq')->getCollection();        
        $this->setCollection($collection);
        parent::_prepareCollection();        
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'=> Mage::helper('autelcorto')->__('Id'),
            'align'     => 'right',
            'width'     => '40px',
            'index'     => 'entity_id',
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }
    
        $this->addColumn('title', array(
            'header'    => Mage::helper('autelcorto')->__('Grupo Faq'),  
            'index'     => 'title',
        ));        

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('autelcorto')->__('Ordinamento'),  
            'index'     => 'sort_order',
            'width'     => '50px',
        ));    
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
}

?>
