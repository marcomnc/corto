<?php
/**
 * Autel shipping table rates collection
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @author     Marco Mancinelli
 */
class Autel_Shipping_Model_Mysql4_Carrier_Auteltablerate_Collection extends Mage_Shipping_Model_Resource_Carrier_Tablerate_Collection
{
    protected function _construct()
    {
        $this->_init('autelshipping/carrier_auteltablerate');
        $this->_shipTable       = $this->getMainTable();
        $this->_countryTable    = $this->getTable('directory/country');
        $this->_regionTable     = $this->getTable('directory/country_region');
    }
    
    public function setCarrireFilter($carrierCode) {
        return $this->addFieldToFilter('carrier_code', $carrierCode);
    }

}
