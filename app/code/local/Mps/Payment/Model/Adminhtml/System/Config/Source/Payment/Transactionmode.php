<?php
/**
 *
 * Test/debug
 * 
 * @category    Payment
 * @package     MPS_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     24-ago-2012
 */
class Mps_Payment_Model_Adminhtml_System_Config_Source_Payment_Transactionmode {
    
    public function toOptionArray() {
        return array (array("value" => 0, "label" => "Live Mode"),
                      array("value" => 1, "label" => "Test Mode"),
                     );
    }
    
}

?>
