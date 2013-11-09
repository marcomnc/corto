<?php

class MpsSistemi_Iplocation_Block_Adminhtml_Zone_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setCollection(Mage::getModel('mpslocation/zonegroup')->getCollection());
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
        
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('mpslocation')->__('Ordine'),
            'index'     => 'sort_order',
            'width'     => '50px',
        ));
        
        $this->addColumn('group_name', array(
            'header'=> Mage::helper('mpslocation')->__('Gruppo'),
            'align'     => 'left',
            'width'     => '500px',
            'index'     => 'group_name',            
        ));

        
            
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }
    
}

?>
