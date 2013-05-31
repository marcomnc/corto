<?php
// Controllers are not autoloaded so we will have to do it manually:
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Autel_Corto_Checkout_OnepageController extends Mage_Checkout_OnepageController
{       
    /**
     * Override della funzione
     */
    public function savePaymentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $placeOrder = $this->getRequest()->getParam('placeOrder');
            $result = $this->getOnepage()->savePayment($data);

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = ($placeOrder=="N")?'':'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
//    /**
//     * Override della funzione
//     */
    public function saveOrderAction()
    {
    	if ($this->_expireAjax()) {
            return;
        }

    	$_items = $this->getOnepage()->getQuote()->getAllItems();
    	$_error = "";
        foreach ($_items as $_item) {        	        	
            try {

                $_sku = $_item->getSku();
                $_qty = $_item->getQty();
                $_size ="U";
                $_available = Mage::getModel("autelcorto/observer")->isProductAvailable($_sku, $_qty, $_size);
                if (!$_available) {
                    $prod = Mage::getModel ("catalog/product")->loadByAttribute("sku", $_sku);
                    $_error = $prod->getName() . " is not available in our stores.\nPlease contact us\n";
                }
            } catch (Exception $e) {
                $prod = Mage::getModel ("catalog/product")->loadByAttribute("sku", $_sku);
                $_error = "General Error for " . $prod->getName();
            }
        }
        
        if ($_error != "") {
            $result = array();
            $result['goto_section'] = 'payment';
            $result['success']  = false;
            $result['error']    = true;
            $result['error_messages'] = $_error;
            $result['block'] = $this->getLayout()->createBlock("core/template")->setCustomMessage($_error)->setTemplate("checkout/onepage/general.error.phtml")->toHtml();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        } else {

            parent::saveOrderAction();
        }
//        
//        //$result = array();
//        //$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }


    
}

?>
