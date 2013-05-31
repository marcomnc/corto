<?php

/**
 * Description of DHLInternational
 *
 * @author marcoma
 */
class Autel_Shipping_Model_Carrier_DHLInternational extends Autel_Shipping_Model_Carrier_Auteltablerate {
    
    protected function _construct() {
        $this->setCode("DHL");
        parent::_construct();
    }
}

?>
