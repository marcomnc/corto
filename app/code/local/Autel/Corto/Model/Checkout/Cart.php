<?php

class Autel_Corto_Model_Checkout_Cart extends Mage_Checkout_Model_Cart
{
    
    /**
     * Rewrite della funziona per fare in modo che quando genera un errore se la richiesta è ajax
     * la risposta sia compatibile con Autel_Corto_Model_Observer::cart_add_product_complete
     *
     * @param   int|Mage_Catalog_Model_Product $productInfo
     * @param   mixed $requestInfo
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProduct($productInfo, $requestInfo=null)
    {        
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);

        $productId = $product->getId();

        $retJson = array();
        
        if ($product->getStockItem()) {
            $minimumQty = $product->getStockItem()->getMinSaleQty();
            //If product was not found in cart and there is set minimal qty for it
            if ($minimumQty && $minimumQty > 0 && $request->getQty() < $minimumQty
                && !$this->getQuote()->hasProductId($productId)
            ){
                $request->setQty($minimumQty);
            }
        }

        if ($productId) {
            try {
                $result = $this->getQuote()->addProduct($product, $request);
            } catch (Mage_Core_Exception $e) {
                $this->getCheckoutSession()->setUseNotice(false);
                $result = $e->getMessage();
            }
            /**
             * String we can get if prepare process has error
             */
            if (is_string($result)) {
                $redirectUrl = ($product->hasOptionsValidationFail())
                    ? $product->getUrlModel()->getUrl(
                        $product,
                        array('_query' => array('startcustomization' => 1))
                    )
                    : $product->getProductUrl();
                $this->getCheckoutSession()->setRedirectUrl($redirectUrl);
                if ($this->getCheckoutSession()->getUseNotice() === null) {
                    $this->getCheckoutSession()->setUseNotice(true);
                }
                if ($requestInfo['ajax'] == 1) {
                    // Se sono in ajax imposto l'errore
                    $retJson["status"]= "KO";
                    $retJson["message"] = $result;
                } else {             
                    Mage::throwException($result);
                }
            }
        } else {
            if ($requestInfo['ajax'] == 1) {
                // Se sono in ajax imposto l'errore
                $retJson["status"]= "KO";
                $retJson["message"] = Mage::helper('checkout')->__('The product does not exist.');
            } else {             
                Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
            }
        }
        
        if (isset($retJson['status'])) {
            // Se ho impostato l'errore lo ritorno come json è mi fermo!
            if (isset($retJson['message'])) {
                $retJson["decode_message"] = base64_encode($retJson['message']);
            }
            Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($retJson));                         
            echo Mage::app()->getResponse()->getBody();
            die();
        }

        Mage::dispatchEvent('checkout_cart_product_add_after', array('quote_item' => $result, 'product' => $product));
        $this->getCheckoutSession()->setLastAddedProductId($productId);
        return $this;
    }

}
