<?php

/**
 * Modulo spedizioniere Pickup
 *
 * @author Marco Mancinelli
 */
class Autel_Shipping_Model_Carrier_Pickup_Milan
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'autel_pikup_in_milan';
    protected $_isFixed = true;

    /**
     * FreeShipping Rates Collector
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $result = Mage::getModel('shipping/rate_result');

        $this->_updateFreeMethodQuote($request);

        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier('autel_pikup_in_milan');
        $method->setCarrierName("Pickup in Paris");
        $method->setCarrierTitle("Pickup in milan");

        $method->setMethod('boutique');
        $method->setMethodTitle('Pickup in Milan');

        $method->setPrice('0.00');
        $method->setCost('0.00');

        $result->append($method);
 
        return $result;
    }

    /**
     * Allows free shipping when all product items have free shipping (promotions etc.)
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return void
     */
    protected function _updateFreeMethodQuote($request)
    {
        $freeShipping = false;
        $items = $request->getAllItems();
        $c = count($items);
        for ($i = 0; $i < $c; $i++) {
            if ($items[$i]->getProduct() instanceof Mage_Catalog_Model_Product) {
                if ($items[$i]->getFreeShipping()) {
                    $freeShipping = true;
                } else {
                    return;
                }
            }
        }
        if ($freeShipping) {
            $request->setFreeShipping(true);
        }
    }

    public function getAllowedMethods()
    {
        return array('freeshipping'=>$this->getConfigData('name'));
    }

}
