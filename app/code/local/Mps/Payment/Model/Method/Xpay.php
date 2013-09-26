<?php
/**
 *
 * Classe Base per la gestione dei pagaenti
 * 
 * @category    Payment
 * @package     MPS_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */

class Mps_Payment_Model_Method_Xpay extends Mage_Payment_Model_Method_Abstract
{    
    protected $_code = "xpay_cartasi";
    // Overide nel caso il modulo utilizzi dei form custom
    //protected $_formBlockType = 'mpspayment/standard_form';
    //protected $_infoBlockType = 'mpspayment/standard_info';
    
    /**
     * Recupero l'url per il test
     */
    protected function _getTestUrl() {
        return "https://ecommerce.keyclient.it/ecomm/ecomm/DispatcherServlet";
    }
    
    /**
     * Importo transazione per test
     * @return type 
     */
    protected function _getAmountTest() {
        return 0.01;
    }
    
    /**
     * Recuperi ii campi del pagamento
     * @return type 
     */
    public function getPaymentFormFields() {
        
        
        $importo =$this->_getTotalTransaction() * 100;
        $this->_debug("Importo transazione $importo");
    	
    	$codTrans = $this->_getLastRealOrderId(); // Valore univoco alfanumerico a propria scelta
        $divisa = 'EUR'; // Valore fisso "EUR"
        
        
        if ($this->getConfigData('transaction_mode')) {
            $chiaveSegreta = "esempiodicalcolomac";
            $alias = "payment_testm_nourlmac";
            $codTrans = date('ymd-His') . '::'. $codTrans; //Per evitare ordini doppi...
        } else {
            $chiaveSegreta = $this->getConfigData("mac"); 
            $alias = $this->getConfigData("alias");
        }
        
        $stringa = 'codTrans='.$codTrans.'divisa='.$divisa.'importo='.$importo.$chiaveSegreta;
        
        $this->_debug("stringa MAC $stringa");
        
        $mac = sha1($stringa);
    	
        $sArr = array(
        	'alias' => $alias,
        	'importo' => $importo,
        	'divisa' => $divisa,
        	'codTrans' => $codTrans,
        	'mail' => $this->_getCustomerMail(),
        	'urlpost' => Mage::getUrl('mpspaygate/payment/success'),
        	'session_id'=> session_id(),
        	'url_back'=> Mage::getUrl('mpspaygate/payment/cancel'),
        	'languageId'=> $this->getConfigData("language"),
        	'mac'=> $mac
        
        );

        $this->_debug("Arary Orginale");
        $this->_debug($sArr);
        
        $rArr = array();
        foreach ($sArr as $k=>$v) {
            /*
            replacing & char with and. otherwise it will break the post
            */
            $rArr[$k] =  str_replace("&","and",$v);
        }
        
        $this->_debug("Array Definitivo");
        $this->_debug($rArr);        

        return $rArr;

    }

    public function processResponse() {
        $_params = $this->getSuccesData("get");
        $_result = true;

        if ($this->getDataConfig("transaction_mode")) {
            $_transArr = split("::", getSuccesData);
            $_trans = $_transArr[1];
        } else {
            $_trans = $_params["codTrans"]; 
        }
        
        $_order = Mage::getModel('sales/order')->loadByIncrementId($_trans);

        if ($_params['esito'] == 'OK')
        {
            if (!$_order->getId())
            {
            	$result = false;
            }
            else
            {
                $_order->getPayment()->setTransactionId($_trans);
                $this->completeTransaction($_order);


            }
	}
        else // Esito diverso da OK
        {

            $result = false;            

        }
        
        return $result;
 
    }

}
?>
