<?php

/*
 * Funzioni Generiche per corto
 */

/**
 * Description of GeneralController
 *
 * @author Marco Mancinelli
 */
class Autel_Corto_GeneralController extends Mage_Core_Controller_Front_Action
{   
    public function setcountryAction() {
        
        $_countryName = $this->getRequest()->getParam("country");
        $_session = Mage::getSingleton("customer/session");
        if ($_countryName != "" && $_session->isLoggedIn()) {
            $_session->setGeoCountry($_countryName);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("result" => "ok")));
    }
    
    /**
     * Delete shoping cart in Ajax
     */
    public function deleteajaxAction()
    {
        $_return = array("result" => "ko",
                         "removeItem" => 0);
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $_cart = Mage::getSingleton('checkout/cart');
                $_cart->removeItem($id)->save();
                $_price = Mage::helper('checkout')->formatPrice($_cart->getQuote()->getGrandTotal());
                $_item = $_cart->getItemsCount();
                $_block = $this->getLayout()->createBlock("checkout/cart_sidebar")->setTemplate("checkout/cart/light.phtml")->toHtml();
                $_links = Mage::app()->getFrontController()->getAction()->loadLayout()->getLayout()->getBlock('top.links')->toHtml();
                $_return = array("result" => "ok",
                                 "removeItem" => $id,
                                 "total" => $_price,
                                 "items" => $_item,
                                 "block" => $_block,
                                 "links" => $_links );
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($_return));
    }
    
     public function lightcouponAction() {
            
            $params = $this->getRequest()->getParams();
            $ret = array();
            $couponCode = "";
            
            if ((!isset($params["code"]) || $params["code"] == "") && (!isset($params["remove"]) || $params["remove"] == "")) {
                $ret["status"] == "NO_ACTION";
            } else {
                if ($params["remove"] == 1) {
                    $couponCode = "";
                } else {
                    $couponCode = $params["code"];
                }
                try {
                    $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
                    $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                        ->collectTotals()
                        ->save();
                    
                    if (strlen($couponCode)) {
                        if ($couponCode == $this->_getQuote()->getCouponCode()) {
                            $ret["status"] = "APPLY";
                            $ret["message"] = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
                            $ret["block"] = $this->getLayout()->createBlock("checkout/cart_sidebar")->setTemplate("checkout/cart/light.phtml")->toHtml();
                        }
                        else {
                            $ret["status"] = "NO_APPLY";
                            $ret["message"] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                        }
                    } else {
                        $ret["status"] = "CANCEL";
                        $ret["message"] = $this->__('Coupon code was canceled.');
                        $ret["block"] = $this->getLayout()->createBlock("checkout/cart_sidebar")->setTemplate("checkout/cart/light.phtml")->toHtml();
                    }
                
            } catch (Exception $e) {
                $ret["status"] = "ERROR";
                $ret["message"] = $this->__('Cannot apply the coupon code.');
                Mage::LogException($e);
            }
                        
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($ret));
     }
     
     public function mediarequestAction() {
         
         $block = $this->getLayout()->createBlock("autelcorto/wpintegration")->setTemplate('corto/wpintegration/mediarequest.phtml');
         $this->getResponse()->setBody($block->toHtmlPopUp());
         
     }
     
     public function mediarequestpostAction() {
         
         $ret = array("status" => "OK", "message" => $this->__("Your request is submitted!"));
         
         $params = $this->getRequest()->getPost();

         if (!isset($params['user_email']) || $params['user_email'] == "" ) {
             $ret = array("status" => "KO", "message" => $this->__("Please specify your email address!"));
         } else {
             $text = "";
             if (isset($params['sender_first_name'])) {
                 $text .= "First Name:  " . $params['sender_first_name'] ."\n";
             }
             
             $text .= "e-mail:  " . $params['user_email'] ."\n";
             
             if (isset($params['sender_role'])) {
                 $text .= "Role:  " . $params['sender_role'] ."\n";
             }
             if (isset($params['sender_magazine'])) {
                 $text .= "Magazione:  " . $params['sender_magazine'] ."\n";
             }
             if (isset($params['sender_message'])) {
                 $text .= "Message:  " . $params['sender_message'] ."\n";
             }             

             //$mailto = "isabel.estarreja@corto.com";
             //$mailto = "marco@mancinellimarco.it,mnc74@hotmail.it";
	    $mailto = "cm@corto.com,press@corto.com";
            if (!mail($mailto,'Richiesta Iscrizione Media',$text,'From: no-reply@corto.com')) {
                $ret = array("status" => "KO", "message" => $this->__("There was an error in your request!"));
            }
         }
                          
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($ret));
     }
     
     private function _getQuote() {
         return Mage::getSingleton('checkout/cart')->getQuote();
     }
     
     public function forgotpasswordAction() {
         $block = $this->getLayout()->createBlock("autelcorto/wpintegration")
                       ->setTemplate('corto/wpintegration/passwordrequest.phtml');
         $this->getResponse()->setBody($block->toHtmlPopUp());
     }
        
     
    public function mailtoAction() {
         $block = $this->getLayout()->createBlock("autelcorto/wpintegration")->setTemplate('corto/wpintegration/mailto.phtml');

         $mailTo = urldecode($this->getRequest()->getParam('address'));
         $mailTo = preg_split("/:/", $mailTo);

         $object = urldecode ($this->getRequest()->getParam('object').'');
         $comment = urldecode ($this->getRequest()->getParam('comment').'');
         $class = urldecode ($this->getRequest()->getParam('class').'');

         if (!isset($mailTo[1])) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Invalid Mail Address', true, 500);
                die();
         }

         $this->getResponse()
              ->setBody($block->setMailTo($mailTo[1])
                              ->setObject($object)
                              ->setComment($comment)
                              ->setClass($class)
                              ->toHtmlPopUp());
     }
     
     public function mailtopostAction() {
         
         $ret = array("status" => "OK", "message" => $this->__("Your request is submitted!"));
         
         $params = $this->getRequest()->getPost();

         if (!isset($params['email']) || $params['email'] == "" ) {
             $ret = array("status" => "KO", "message" => $this->__("Please specify your email address!"));
         } else {
             $text = "";
             if (isset($params['name'])) {
                 $text .= "Name:  " . $params['name'] ."\n";
             }
             
             $text .= "e-mail:  " . $params['email'] ."\n";
             
             if (isset($params['telephone'])) {
                 $text .= "Telephone:  " . $params['telephone'] ."\n";
             }
             
             if (isset($params['comment'])) {
                 $text .= "Message:  " . $params['comment'] ."\n";
             }             

            $mailto = $params['to'];             
	    $object = 'Richiesta Informazione';
            if (isset($params['object'])){
                $object = $params['object'];
            }
            if (!mail($mailto,$object,$text,'From: no-reply@corto.com')) {
                $ret = array("status" => "KO", "message" => $this->__("There was an error in your request!"));
            }
         }
                          
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($ret));
     }
     
     
     public function fancyAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock("show-fancy-popup");            
        $this->renderLayout();    
     }
}

?>
