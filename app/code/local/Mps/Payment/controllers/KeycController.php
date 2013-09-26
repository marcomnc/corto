<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * Description of KeycController
 * 
 * @category    default
 * @package     
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     30-ago-2012
 */
class Mps_Payment_KeycController extends Mage_Core_Controller_Front_Action  {
    
    protected $_paymentModel = null;

    protected function _getModel() {
        if ($this->_paymentModel == null) {
            $this->_paymentModel = Mage::getModel(Mage::getStoreConfig("payment/key_client/model"));
        }
        return $this->_paymentModel;
    }

    protected function _debug($message) {
        $this->_getModel()->debugData($message);
    }

    
    /**
     * Indirizzo da utilizzare per il successo nella versione di risposta on line
     * La richiesta viene fatta in modo tale che la banca risponda nei due canali.
     * in success vengono intercettati i dati in GET e viene notificato all'utente 
     * l'esito della transazione
     * 
     * In transaction vengono intercettati i dati in POST e viene processato l'ordine
     */
    public function successAction() {
                        
        $_params = $this->getRequest()->getParams();
        $this->_debug("Accetto richiesta server to client");
        $this->_debug($_params);
        if (strtoupper($_params["esito"]) == "OK") {    
            $session = Mage::getSingleton('checkout/session');
            $session->setQuoteId($session->getMpsPaymentQuoteId(true));
            Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

            $this->_redirect('checkout/onepage/success', array('_secure'=>true));
        } else  {
            //TEnto di cancellare ugualmente l'ordine perchè potrei non avere la transazione 
            //server to server     
            $this->_debug("Tento di cancellare l'ordine server to server");  
            $this->_getModel()->setTransactionParams($_params)
                              ->setTransactionType("client")
                              ->processServerResponse();
            $this->_redirect('checkout/onepage/failure');
        }
    }
    
    /**
     * Indirizzo da utilizzare per la comunicazione server tu server per chiudere l'ordine
     */
    public function transactionAction() {

        $this->_debug("Accetto richiesta server to server");
        
        if (!$this->getRequest()->isPost()) {
            $this->_debug("Accetto richiesta server to server sona parametri di POST");
            return;            
        }
        
        try {
            $_params = $this->getRequest()->getPost();            
            $this->_debug($_params);  
            $this->_getModel()->setTransactionParams($_params)
                              ->setTransactionType("server")
                     ->processServerResponse();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        


    }
}

?>
