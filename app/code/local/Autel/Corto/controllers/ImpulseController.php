<?php
class Autel_Corto_ImpulseController extends Mage_Core_Controller_Front_Action
{
    
    private function _initMessage() {
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
    }
    
    public function indexAction() {	

        $this->loadLayout();
        $this->getLayout()->getBlock("cortoImpulseBuy");            
        $this->renderLayout();            
    }

    //Se riesco visualizzo il prodotto che ti voglio fare acquistare
    public function impulseAction() {
        $this->_initMessage();
        
        $params = $this->getRequest()->getParams();

        $redirectUrl = Mage::getUrl("*/*/");

        if ((isset($params["destination"]) && $params["destination"].""!= "") || (isset($params["firstlove"]) && $params["firstlove"].""!= "") ||  (isset($params["livewithout"]) && $params["livewithout"].""!= "")) {            
            $p = MAge::getModel("autelcorto/impulse")->ImpulseBuy();
            if ($p !== false) {
                //Mage::getSingleton('checkout/session')->addSuccess(Mage::helper('catalog')->__('Your impulse product is ' . $p->getDescription() ));            
                $this->loadLayout();
                $this->getLayout()->getBlock("cortoImpulseBuyAdd")->setProduct($p);            
                $this->renderLayout();     
                
            } else {
                Mage::getSingleton('core/session')->addError(Mage::helper('catalog')->__('Sorry, today is not good day for buy'));
                $redirectUrl = ($this->_getRefererUrl())?$this->_getRefererUrl():$redirectUrl;
                $this->getResponse()->setRedirect($redirectUrl);
            }
        } else {
            Mage::getSingleton('core/session')->addError(Mage::helper('catalog')->__('Sorry, Let\'s Start'));
            $redirectUrl = ($this->_getRefererUrl())?$this->_getRefererUrl():$redirectUrl;
            $this->getResponse()->setRedirect($redirectUrl);
        }
        
        
    }
    
    public function addtoAction() {
        $id = $this->getRequest()->getParam('id');
        
        $redirectUrl = Mage::getUrl("*/*/");
        
        $product = Mage::getModel('catalog/product')->Load($id);
        
        if (Mage::getModel('autelcorto/impulse')->ImpulseBuyAdd($product)) {
                
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('catalog')->__('Congratulation %s has been added to your shopping bag' , $product->getName()  ));            
            
        } else {
        
            Mage::getSingleton('core/session')->addError(Mage::helper('catalog')->__('Sorry, today is not good day for buy'));
        }
        
        $this->getResponse()->setRedirect($redirectUrl);
    }
}

?>
