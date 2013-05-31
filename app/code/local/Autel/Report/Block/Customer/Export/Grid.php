<?php

class Autel_Report_Block_Customer_Export_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        $this->setFilterVisibility(false);
        parent::__construct();
        //$this->setCountTotals(true);
        
    }

    public function _prepareCollection()
    {
        $collection = new Varien_Data_Collection(); 
        $customers = Mage::getModel('customer/customer')->getCollection();
        foreach ($customers as $cust) {
            $customer = Mage::getModel('customer/customer')->Load($cust->getId());
            
            $rowItem = clone ($customer);
            $addressId = $customer->getDefaultBilling();
            if ($addressId) {
                $address = Mage::getModel('customer/address')->Load($addressId);
                $rowItem->setData('company', $address->getData('company'));
                $rowItem->setData('city', $address->getData('city'));
                $country = Mage::getModel('directory/country')->loadByCode($address->getData('country_id'));
                if ($country) {
                    $rowItem->setData('country', $country->getName());
                } else {
                    $rowItem->setData('country', "");
                }
                $rowItem->setData('postcode', $address->getData('postcode'));
                $region = Mage::getModel('directory/region')->load($address->getData('region_id'));
                if ($region && $address->getData('region_id') != 0) {
                    $rowItem->setData('region', $region->getName());
                } else {
                    $rowItem->setData('region', $address->getData('region_id'));
                }
                $rowItem->setData('street', $address->getData('street'));
            }            
            
            $order = Mage::getModel('sales/order')->getCollection();
            $adapter = $order->getConnection();            
            $order->getSelect()->where('main_table.customer_id = ' . $customer->getId())
                               ->where('main_table.status not in (?)', Mage::getSingleton('sales/config')
                                                                ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED))
                               ->where('main_table.state not in (?)', array(Mage_Sales_Model_Order::STATE_NEW,
                                                                   Mage_Sales_Model_Order::STATE_PENDING_PAYMENT));
            $selectStr = sprintf('%s + %s ',
                                    $adapter->getIfNullSql('main_table.base_subtotal_incl_tax', 0),
                                    $adapter->getIfNullSql('main_table.base_discount_amount', 0)
                                );
            $order->getSelect()->reset(Zend_Db_Select::COLUMNS)
                               ->columns(array('orderd' => $selectStr,
                                               'number' => 'count(*)'));
            foreach ($order as  $ord) {
                $rowItem->setData('ordered', $ord->getOrderd());
                $rowItem->setData('number', $ord->getNumber());
            }
            
            $rowItem->setData('from_fb',  (!is_null($customer->getFbUid())) ? 'Yes' : 'No');
            $collection->addItem($rowItem);
        }
        
        $this->setCollection($collection);
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header'        => Mage::helper('customer')->__('FirstName'),
            'index'         => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('customer')->__('LastName'),
            'index'     => 'lastname',            
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('email'),
            'index'     => 'email',            
        ));

        $this->addColumn('register', array(
            'header'    => Mage::helper('autelrpt')->__('Register At'),
            'index'     => 'created_at',
            'type'      => 'date',            
        ));
        
        $this->addColumn('from_fb', array(
            'header'    => Mage::helper('autelrpt')->__('From FB'),
            'index'     => 'from_fb',           
        ));
        
        $this->addColumn('company', array(
            'header'    => Mage::helper('customer')->__('Company'),
            'index'     => 'company',            
        ));
        $this->addColumn('city', array(
            'header'    => Mage::helper('customer')->__('City'),
            'index'     => 'city',            
        ));

        $this->addColumn('country', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'index'     => 'country',
        ));        

        $this->addColumn('region', array(
            'header'    => Mage::helper('customer')->__('Region'),
            'index'     => 'region',
        ));        
        
        $this->addColumn('postcode', array(
            'header'    => Mage::helper('customer')->__('Postcode'),
            'index'     => 'postcode',
        ));        
        
        $this->addColumn('street', array(
            'header'    => Mage::helper('customer')->__('Street'),
            'index'     => 'street',
        ));        

        $this->addColumn('ordered', array(
            'header'    => Mage::helper('autelrpt')->__('Total Order'),
            'index'     => 'ordered',
            'type'      => 'currency',
            'currency'  => 'base_currency_code',
        ));        

        $this->addColumn('number', array(
            'header'    => Mage::helper('customer')->__('Number Order'),
            'index'     => 'number',
        ));        

        
        $this->addExportType('*/*/exportlist/exp_type/xls/','Excel XML');
        $this->addExportType('*/*/exportlist/exp_type/csv/', 'CSV');        

        return parent::_prepareColumns();
    }
}
