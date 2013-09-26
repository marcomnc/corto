<?php
/**
 *
 * Description of From
 * 
 * @category    Payment
 * @package     MPS_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     24-ago-2012
 */
class Mps_Payment_Block_Form extends Mage_Payment_Block_Form {
    
    protected function _construct() {
        $this->setTemplate("mpspayment/base/form.phtml");
        parent::_construct();
    }
}

?>
