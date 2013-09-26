<?php

/**
 *
 * Tabella di dettaglio dei pagamenti effettuati
 * 
 * @category    Payment
 * @package     Mps_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */
class Mps_Payment_Model_Mysql4_Transactionstatus extends Mps_Paymnet_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('mpspayment/transactionstatus', 'entity_id');
    }

}

?>