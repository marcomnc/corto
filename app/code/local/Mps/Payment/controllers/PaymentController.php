<?php
/**
 *
 * Controller base per il modulo di pagamento. 
 * 
 * @category    Payment
 * @package     MPS_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     23-ago-2012
 */
class Mps_Payment_PaymentController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Recuper il blocco di redirect associato al metodo di pagamento se previsto
     * @param type $method
     * @return string 
     */
    private function _getBlock($method) {
        $_block = Mage::getStoreConfig("payment/" . $method . '/redirect_block');
        if ($_block === "") {
            $_block = "mpspayment/redirect";
        } else {
            $_block = $_block;
        }
        return $_block;
    }
    
    /**
     * Avvio il pagamento
     */
    public function redirectAction () {
        $_method = $this->getRequest()->getParam("_method");
        $session  = Mage::getSingleton("checkout/session");
        $session->setMpsPaymentQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock($this->_getBlock($_method))->toHtml());
    }
    
    /**
     * Ritento il pagamento
     * @todo .... 
     * Da sistemare per capire da dove recuperare i dati. eventualmente salvare i dati passati alla banca e
     * rimandarli......
     */
    public function rewardAction() {
        $_quoteId = $this->getRequest()->getParam("id");
Mage::Log($this->getRequest()->getParams());
        Mage::getSingleton("checkout/session")->setQuoteId($_quoteId);
Mage::Log(Mage::getSingleton("checkout/session"));
        $_method = $this->getRequest()->getParam("_method");
        $this->getResponse()->setBody($this->getLayout()->createBlock($this->_getBlock($_method))->toHtml());
    }
    
    /**
     * Indirizzo da utilizzare per la cancellazione dell'ordine (quando la banca annulla l'ordine)
     * Comune a tutti i metodi
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        //Memorizzo i parametri per eventuali controlli/log
        $_params = $this->getRequest()->getParams();
        $session->setResponseParameter($_params);
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            $_payment = $order->getPayment()->getMethodInstance();
            $_payment->debugData("Annullamento transazione");
            $_payment->debugData($_params);
            if ($order->getId()) {
                //Nel metodo cancel del pagamento imposto eventuali commenti
                $order->cancel()->save();
            }
        }
        $session->unsResponseParameter();
        $this->_redirect('checkout/cart');
    }
    
}

?>
