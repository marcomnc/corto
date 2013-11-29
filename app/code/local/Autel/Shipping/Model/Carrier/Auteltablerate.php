<?php

/**
 * Modulo spedizioniere DHL
 *
 * @author Marco Mancinelli
 */
class Autel_Shipping_Model_Carrier_Auteltablerate extends Mage_Shipping_Model_Carrier_Abstract {

    /**
     * @ToDo Rendere eventualmente parametrica
     */
    const SHIPPING_CONDITION = "package_weight";
    
    /**
     * unique internal shipping method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'autel_dhl_shipping';
    protected $_default_condition_name = 'package_weight';
    protected $_carrierCode = 'DHL';

    protected $_conditionNames = array();

    public function __construct()
    {
        parent::__construct();
//        foreach ($this->getCode('condition_name', self::SHIPPING_CONDITION) as $k=>$v) {
//            $this->_conditionNames[] = $k;
//        }
        $this->_conditionNames[]  = self::SHIPPING_CONDITION;
    }
    
    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field)
    {
        if (empty($this->_code)) {
            return false;
        }
        $path = 'carriers/'.$this->_code.'/'.$field;
        return Mage::getStoreConfig($path, $this->getStore());
    }
    
    public function getCode($type, $code='')
    {
        $codes = array(

            'condition_name'=>array(
                'package_weight' => Mage::helper('shipping')->__('Weight vs. Destination'),
                'package_value'  => Mage::helper('shipping')->__('Price vs. Destination'),
                'package_qty'    => Mage::helper('shipping')->__('# of Items vs. Destination'),
            ),

            'condition_name_short'=>array(
                'package_weight' => Mage::helper('shipping')->__('Weight (and above)'),
                'package_value'  => Mage::helper('shipping')->__('Order Subtotal (and above)'),
                'package_qty'    => Mage::helper('shipping')->__('# of Items (and above)'),
            ),

        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Table Rate code type: %s', $type));
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Table Rate code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }

    
    protected function setCode($_carrier) {
        $this->_carrierCode = strtoupper($_carrier);
        $this->_code = 'autel_' . strtolower($_carrier)  .'_shipping';
        return $this;
    }

    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        // skip if not enabled
        if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
            return false;
        }

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                        foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeQty += $item->getQty() * ($child->getQty() - (is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0));
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeQty += ($item->getQty() - (is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0));
                }
            }
        }

        if (!$request->getConditionName()) {
            $request->setConditionName($this->getConfigData('condition_name') ? $this->getConfigData('condition_name') : $this-> _default_condition_name);
        }

         // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        $result = Mage::getModel('shipping/rate_result');
        $rate =  Mage::getResourceModel('autelshipping/carrier_auteltablerate')
                    ->setCarrierCode($this->_carrierCode)
                    ->getRate($request);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

// Forzato il ritoprno sempre a 0!
        
//        if (!empty($rate) && $rate['price'] >= 0) {
//            $method = Mage::getModel('shipping/rate_result_method');
//
//            $method->setCarrier($this->_code);
//            $method->setCarrierTitle($this->getConfigData('title'));
//
//            $method->setMethod('bestway');
//            //$_methodDescription
//            $_methodDescription = "";       
//
//            if ($request->getFreeShipping() === true || ($request->getPackageQty() == $freeQty)) {
//                $shippingPrice = 0;
//            } else {
//                $_totalShippingPrice = $rate['price'];
//                if (!is_null($rate['surcharge']) && $rate['surcharge'] != 0) {
//                    $_totalShippingPrice += $rate['price'] * $rate['surcharge'] / 100;
//                    $_methodDescription .= "\n" . Mage::helper('shipping')->__("Fuel Surcharge") . ": " . Mage::helper('core')->currency(( $rate['price'] * $rate['surcharge'] / 100),true,false) ;
//                }
//                if (!is_null($rate['insurance']) && $rate['insurance'] != 0) {
//                    $_totalShippingPrice += $rate['price'] * $rate['insurance'] / 100;
//                    $_methodDescription .= "\n" . Mage::helper('shipping')->__("Insurance") . ": " . Mage::helper('core')->currency(( $rate['price'] * $rate['insurance'] / 100),true,false) ;
//                }
//                if (!is_null($rate['advance']) && $rate['advance'] != 0) {
//                    $_totalShippingPrice += $rate['advance'] ;
//                    $_methodDescription .= "\n" . Mage::helper('shipping')->__("Anticipo Documenti Doganali") . ": " . Mage::helper('core')->currency($rate['advance'] ,true,false) ;
//                }
//                if (!is_null($rate['cites']) && $rate['cites'] != 0) {
//                    $_totalShippingPrice += $rate['cites'] ;
//                    $_methodDescription .= "\n" . Mage::helper('shipping')->__("Cites") . ": " . Mage::helper('core')->currency($rate['cites'] ,true,false) ;
//                }
//                if (!is_null($rate['cites_custom']) && $rate['cites_custom'] != 0) {
//                    $_totalShippingPrice += $rate['cites_custom'] ;
//                    $_methodDescription .= "\n" . Mage::helper('shipping')->__("Pratica Cites per Dogana") . ": " . Mage::helper('core')->currency($rate['cites_custom'] ,true,false) ;
//                }
//                $shippingPrice = $this->getFinalPriceWithHandlingFee($_totalShippingPrice);
//            }
//            
//            if ($_methodDescription != "") {
//                $_methodDescription = "\n" . Mage::helper('shipping')->__("Comprensivo di") .  $_methodDescription;
//            }
//            $method->setMethodTitle($this->getConfigData('name') );
//            $method->setMethodDescription($_methodDescription);
//            
//            $method->setPrice($shippingPrice);
//            $method->setCost($rate['cost']);
//
//            $result->append($method);         
//        }
        
        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod('bestway');
        $shippingPrice = 0;
        //$_methodDescription
        $_methodDescription = "";  
        
        $method->setMethodTitle($this->getConfigData('name') );
        $method->setMethodDescription($_methodDescription);

        $method->setPrice($shippingPrice);        
        $method->setCost(0);

        $result->append($method);     

        return $result;
    }

    /**
     * This method is used when viewing / listing Shipping Methods with Codes programmatically
     */
    public function getAllowedMethods() {
        return array($this->_code => $this->getConfigData('name'));
    }

}

?>
