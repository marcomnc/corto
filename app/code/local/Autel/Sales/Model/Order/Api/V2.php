<?php

/**
 * Catalog product attribute set api V2
 *
 * @category   Autel
 * @package    Autel_Sales
 * @author     Marco Mancinelli
 */

class Autel_Sales_Model_Order_Api_V2 extends Mage_Sales_Model_Order_Api_V2
{
    protected $_helper = null;

    public function __construct() {
        $this->_helper = Mage::helper("autelsales");
        parent::__construct();
    }

    public function getOrderAdditionalInfo($incrementId) {
        $_returnOrder = array("order_id"                        => 0,
                              "increment_id"                    => "",
                              "tax_code"                        => "",
                              "billing_country_description"     => "",
                              "shipping_country_description"    => "", 
                              "billing_customer_cf"             => "",
                              "shipping_customer_cf"            => "",
                            );      
        
        $_myOrder = Mage::getModel("sales/order")->loadByIncrementId($incrementId);
        
        if ($_myOrder->getId() > 0) {
            $_returnOrder["order_id"] = $_myOrder->getId();
            $_returnOrder["increment_id"] = $_myOrder->getIncrementId();
            
            $_shipping = $_myOrder->getShippingAddress();
            $_returnOrder["shipping_country_description"] = Mage::getModel('directory/country')->Load($_shipping->getCountryId())->getName();
          
            $_billing = $_myOrder->getBillingAddress();
            $_returnOrder["billing_country_description"] = Mage::getModel('directory/country')->Load($_billing->getCountryId())->getName();            

            $_returnOrder["tax_code"] = $this->_getTypeIva($_myOrder);
        }
        
        return $_returnOrder;
    }

    public function info($orderIncrementId) {
       $result = parent::info($orderIncrementId);
       Mage::app()->setCurrentStore($result["store_id"]);
       $rateCollections = Mage::getModel("sales/quote")->Load($result["quote_id"])->getShippingAddress()->getShippingRatesCollection()->getItems();

       foreach ($rateCollections as $k => $rate) {
           if ($rate->getCode() == $result["shipping_method"]) {
                $config = Mage::getStoreConfig('carriers/'.$rate->getCarrier(), $result["store_id"]);
                if (isset($config["is_pikup"]) && $config["is_pikup"] == 1) {
                    //Sostituisco tutte le impostazioni dell'adress boutique
                    $result["shipping_address"]["address_id"] = "";
                    foreach ($result["shipping_address"] as $k => $v) {             
                        if (isset($config[$k])) {
                            $result["shipping_address"][$k] = $config[$k];
                        }                      
                    }
                }
                break;
           }
       }
       
       if (Mage::getStoreConfig('autelconnector/connector_sales/duty_calculator_active')) {
           Foreach ($result['items'] as $item) {
               /**
                * @todo Verificare per prodotti configurabili!!!!!
                */               
               $hc = Mage::Helper('autelcorto/duty')->getCodeFromProduct($item['product_id'], $result['shipping_address']['country_id']);
               if ($hc !== false) {
                   $hcItem = array(
                       'item_id'        => '0', //Indica che è un prodotto aggiunto
                       'order_id'       => $result['order_id'], 
                       'quote_item_id'  => $item['item_id'],   //Item di riferimento
                       'product_type'   => 'harmonized', //indica che è l'harmonized code
                       'product_id'     => $item['product_id'],
                       'sku'            => $hc,
                       'name'           => '',
                   );
                   
                   $result['items'][] = $hcItem;
               }
               
           }
       }
       
       return $result;
   }
    
    private function _getTypeIva($order) {
        $_addressType = Mage::getStoreConfig("tax/calculation/based_on");
        $_country = "";
        switch ($_addressType) {
            case "shipping":
                    $_country = $order->getShippingAddress()->getCountryId();
                break;
            case "billing":
                    $_country = $order->getbillingAddress()->getCountryId();
                break;
            default:
                    $_country = MAge::getStoreConfig("tax/defaults/country");
                break;            
        }
        
        $_vatType = "EXT";
        if ($_country == "IT") {
            $_vatType = "IT";
        } else {
            if (strpos(",". $_country .",", "," . Mage::getStoreConfig("general/default/eu_countries") .",") !== false) {
                $_vatType ="UE";
            }
        }        
        return $_vatType;
    }
}
?>
