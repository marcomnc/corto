<?php
/**
 *
 * Classe Base per la gestione dei pagaenti
 * 
 * @category    Payment
 * @package     MPS_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */

class Mps_Payment_Model_Method_Triveneto extends Mage_Payment_Model_Method_Abstract
{    
    protected $_code = "consorzio_triveneto";
    // Overide nel caso il modulo utilizzi dei form custom
    //protected $_formBlockType = 'mpspayment/standard_form';
    //protected $_infoBlockType = 'mpspayment/standard_info';
}
?>
