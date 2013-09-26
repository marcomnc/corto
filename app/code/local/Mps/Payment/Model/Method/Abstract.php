<?php
/**
 *
 * Classe Base per la gestione dei pagamenti
 * 
 * @category    Payment
 * @package     MPS_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */

abstract class Mps_Payment_Model_Method_Abstract extends Mage_Payment_Model_Method_Abstract
{        
    protected $_formBlockType = 'mpspayment/form';
    protected $_infoBlockType = 'mpspayment/info';
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;
    
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    
    protected $_config = null;
    protected $_testMode = false;
    
    /**
     * Contiene l'ordine nel caso i dati vengano recuperati dall'ordine
     * @todo
     */
    protected $_dataFormOrder = null;
    
    /**
     * Ridefinire l'array per le valute abilitate (Se vuoto tutte)
     */
    protected $_supportedCurrencyCodes = array("EUR");
    
    /**
     * Array contente il codice valuta richiesto dalla banca se diverso da quello di magento
     * l'array è cosi composto:
     * array ("<codice magento>" => "<codice banca>")
     */
    protected $_translateCurrencyCode = Array();
    
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    protected function _getQuote()
    {
        return $this->_getCheckout()->getQuote();
    }
    
    /**
     * Recupera la mail dell'utente associata all'oridine
     * @todo
     */
    protected function _getCustomerMail() {
        $_mail = "marco@mancinellimarco.it";
        if (!$this->getConfigData('transaction_mode')) {            
            $_mail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
        }
        return $_mail;
    }
    
    protected function _getLastRealOrderId() {
        return $this->_getCheckout()->getLastRealOrderId();
    }
    
    /**
     * Recupero il totale della transazione 
     */
    protected function _getTotalTransaction() {
        $_impTrans = $this->_getAmountTest();
        if (!$this->getConfigData('transaction_mode')) {   
            
            $_order = Mage::getModel("sales/order")->loadByIncrementId($this->_getCheckout()->getLastRealOrderId());
            
            $_impTrans = $_order->getBaseGrandTotal();
            
        }
        return $_impTrans;
    }
    
    
    
    public function __construct() {    
        
    } 
    
    /**
     * Whether method is available for specified currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode) {
        return in_array($currencyCode, $this->_supportedCurrencyCodes) || sizeof($this->_supportedCurrencyCodes) == 0 || $this->_supportedCurrencyCodes === null;
    }
    
    /**
     * Controllo se il metodo e abilitato. 
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (parent::isAvailable($quote) && $this->getConfigData("active")) {
            return true;
        }
        return false;
    }
    
    
    /**
     * Ritorna la valuta da utilizzare per la banca
     * 
     * @param string $currencyCode
     * @return string
     */
    public function getCurrencyBank($currencyCode) {
        if (sizeof($this->_translateCurrencyCode) > 0 && in_array($currencyCode, $this->_translateCurrencyCode)) {
            $curencyCode = $this->_translateCurrenctCode[$currencyCode];
        }
        return $currencyCode;
    }
    
    /**
     * Recupera l'indirizzo della banca a cui effettuare la chiamata
     * 
     * @return type 
     */
    public function getGatewayUrl()
    {    	
        return (!$this->getConfigData('transaction_mode'))?$this->getConfigData('url'):$this->_getTestUrl();
    }
    
    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('mpspaygate/payment/redirect', array("_method" => $this->_code));
    }
    
    public function initialize($paymentAction, $stateObject)
    {
        $state = $this->getConfigData('order_status');
        if ($state == "") {
            $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        }
        $stateObject->setState($state);
        $stateObject->setStatus(Mage::getSingleton('sales/order_config')->getStateDefaultStatus($state));
        $stateObject->setIsNotified(false);
    }
    
    /**
     * Verifiche di base per vedere se è permesso effettuare il pagemtno se l'ordine è pending
     * @todo 
     * Una volta sistemro i reward ripristinare
     */
    public function permitCompletePayment($order) {
        return false;
        $_permit = false;
        if ($this->getConfigData('complete_payment')) {
            $state = $this->getConfigData('order_status');
            if ($state == "") {
                $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
            }
            if (!$order instanceof Mage_Sales_Model_Order) {
                $order = Mage::getModel("sales/order")->Load($order);
            }
            if ($order->getStatus() == $state) {
                $_permit = true;
            }
        }
        return $_permit;
    }
    
    public function setTransactionId($_order) {
        
    }
    
    public function completeTransaction(&$order, $commentPlus = array() ) {
        $_status = $this->getConfigData("order_status_after");
        $_state = Mage_Sales_Model_Order::STATE_PROCESSING;
        $_generateInvoice = false || $this->getConfigData("generate_invoice_after");
        
        if ($_status == "") {
            $_status = "payment_done";
        }
        
        $_comment = "";
        if ($_generateInvoice && $order->canInvoice()) {
            try {
                $this->_debug("Inizio fatturazione ordine " . $order->getIncrementId());
                $invoice = $order->prepareInvoice();
                $invoice->register()->capture();

                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();

                $_state = Mage_Sales_Model_Order::STATE_PROCESSING;
                $_comment = Mage::Helper("mpspayment")->__('\nInvoice #%s created', $invoice->getIncrementId());
            } catch (Exception $e) {
                Mage::logException($e);
                $_state = Mage_Sales_Model_Order::STATE_PROCESSING;
                $_comment = Mage::Helper("mpspayment")->__("\nSi è verificato un errore in fase di generazione della fattura");
            }

        }
        $_statusComment = Mage::Helper("mpspayment")->__('Transazione avvenuta con Successo!');
        foreach ($commentPlus as $k => $v) {
            $_statusComment .= "\n$k: $v";
        }
        $_statusComment .= "\n$_comment";
        $order->setState(
                $_state, $_status,
                $_statusComment,
            $notified = true
        );
        
        $order->save();
        $order->sendNewOrderEmail();
    }
    
    /**
     * Cancello l'ordine perchè l'esito non è andato a buon fine
     * @param type $comment 
     */
    public function cancelOrder(&$order, $comment = "") {

        if ($order->canCancel()) {
            $this->_debug("Ordine Cancellabile");
            $history = $order->addStatusHistoryComment($comment, false); // no sense to set $status again
            $history->setIsCustomerNotifiedf(false); // for backwards compatibility
            $order->cancel()                  
                  ->save();                
        }
    }
    
}
?>
