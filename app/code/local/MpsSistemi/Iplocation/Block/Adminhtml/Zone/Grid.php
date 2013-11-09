<?php

class MpsSistemi_Iplocation_Block_Adminhtml_Zone_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $collection = Mage::getModel('mpslocation/zone')->getCollection();
        $collection->getSelect()
                   ->join (array('website' => Mage::getSingleton('core/resource')
                                                    ->getTableName('core/website')),
                            'main_table.website_id = website.website_id', 
                            "concat(code, '-', name) ws_name, code")  
                    ->join (array('group' => Mage::getSingleton('core/resource')
                                                    ->getTableName('mpslocation/zonegroup')),
                            'main_table.group_id = group.entity_id', 
                            array("group_name", "group.sort_order as sort_order_group"));        
        $this->setCollection($collection);
        parent::_prepareCollection();       
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'=> Mage::helper('mpslocation')->__('Id'),
            'align'     => 'right',
            'width'     => '40px',
            'index'     => 'entity_id',            
        ));

        $this->addColumn('code', array(
            'header'=> Mage::helper('mpslocation')->__('Ws Code'),
            'align'     => 'right',
            'width'     => '40px',
            'index'     => 'code',            
        ));

        $this->addColumn('ws_name', array(
            'header'    => Mage::helper('mpslocation')->__('Website'),
            'index'     => 'ws_name',
            'width'     => '200px',
            'filter'    => false,
        ));
    
        $this->addColumn('Code', array(
            'header'    => Mage::helper('mpslocation')->__('Codice Zona'),  
            'index'     => 'zone_code',
            'width'     => '100px',
        ));        
        
        $this->addColumn('Description', array(
            'header' => Mage::helper('mpslocation')->__('Descrizione'),
            'index'  => 'description',
            'width'  => '400px',
        ));
        
        $this->addColumn('group_name', array(
            'header'=> Mage::helper('mpslocation')->__('Gruppo'),
            'width'     => '80px',            
            'index'     => 'group_name',
        ));

        $this->addColumn('sort_order_group', array(
            'header'=> Mage::helper('mpslocation')->__('Ord. Gruppo'),
            'align'     => 'right',
            'width'     => '40px',            
            'index'     => 'sort_order_group',
            'filter'    => false,
        ));
        
        
        $this->addColumn('sort_order', array(
            'header'=> Mage::helper('mpslocation')->__('Ord. Zona'),
            'align'     => 'right',
            'width'     => '40px',            
            'index'     => 'sort_order',
        ));
        $this->addExportType('*/*/export/exp_type/csv/', 'CSV');
        $this->addExportType('*/*/export/exp_type/xls/','Excel XML');
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id');

        $this->getMassactionBlock()
             ->addItem('change_group', array(
                                'label'=> Mage::helper('mpslocation')->__('Change Group'),
                                'url'  => $this->getUrl('*/*/masschangegroup', array('' => '')),
                                'additional' => array(
                                    'visibility' => array(
                                                    'name' => 'group_id',
                                                    'type' => 'select',
                                                    'class' => 'required-entry',
                                                    'label' => Mage::helper('catalog')->__('Status'),
                                                    'values' => MAge::getModel('mpslocation/zonegroup')->getCollection()->toOptionArray()
                                                  )
                                )
            ));
        return $this;
    }
}

?>
