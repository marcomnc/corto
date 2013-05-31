<?php

/**
 * Description of DHLInternational
 *
 * @author marcoma
 */
class Autel_Shipping_Model_Carrier_TNTInternational extends Autel_Shipping_Model_Carrier_Auteltablerate {
    
    protected function _construct() {
        $this->setCode("TNT");
        parent::_construct();
    }
}

?>
