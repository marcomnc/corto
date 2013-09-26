<?php
/**
 *
 * Payment detail Collection
 * 
 * @category    Payment
 * @package     Mps_Paymnet
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */
class Mps_Payment_Model_Mysql4_Transactionstatus_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
            $this->_init('mpspayment/');
    }
}

?>
