<?php
/**
 *
 * Classe gestione KeyClient
 * 
 * @category    Payment
 * @package     MPS_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */

class Mps_Payment_Model_Method_Keyc extends Mps_Payment_Model_Method_Abstract
{    
    protected $_code = "key_client";
    // Overide nel caso il modulo utilizzi dei form custom
    //protected $_formBlockType = 'mpspayment/standard_form';
    //protected $_infoBlockType = 'mpspayment/standard_info';
    
    protected $_supportedCurrencyCodes = array();
    
    /**
     * Recupero l'url per il test
     */
    protected function _getTestUrl() {
        return "https://ecommerce.cim-italia.it/ecomm/DispatcherServlet";
    }
    
    /**
     * Importo transazione per test
     * @return type 
     */
    protected function _getAmountTest() {
        return 0.01;
    }
    
    public function getPaymentFormFields() {
        
        
        $importo =$this->_getTotalTransaction() * 100;
        $this->_debug("Importo transazione $importo");
    	
    	$codTrans = $this->_getLastRealOrderId(); // Valore univoco alfanumerico a propria scelta
        $divisa = 'EUR'; // Valore fisso "EUR"
        
        
        if ($this->getConfigData('transaction_mode')) {
            $chiaveSegreta = "esempiodicalcolomac";
            $alias = "payment_testm_nourlmac";
            $codTrans =  $codTrans . '::' . date('ymd-His'); //Per evitare ordini doppi...
        } else {
            $chiaveSegreta = $this->getConfigData("mac"); 
            $alias = $this->getConfigData("alias");
        }
        
        $stringa = 'codTrans='.$codTrans.'divisa='.$divisa.'importo='.$importo.$chiaveSegreta;
        
        $this->_debug("stringa MAC $stringa");
        
        $hash = sha1($stringa);
        //$b64 = base64_encode($hash);
        //$urlEncode = urlencode($b64);

        $mac = $hash; //$urlEncode;
    	
        $sArr = array(
        	'alias' => $alias,
        	'importo' => $importo,
        	'divisa' => $divisa,
        	'codTrans' => $codTrans,
        	'mail' => $this->_getCustomerMail(),
        	'urlpost' => Mage::getUrl('mpspaygate/keyc/transaction'),
                'url' => Mage::getUrl('mpspaygate/keyc/success'),                
                'urlpost' => Mage::getUrl('mpspaygate/keyc/transaction'),                
                'session_id'=> session_id(),
        	'url_back'=> Mage::getUrl('mpspaygate/keyc/cancel'),
        	'languageId'=> $this->getConfigData("language"),        
                'session_id'=> Mage::getSingleton("core/session")->getSessionId(),
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

    /**
     * Pocesso l'ordine 
     */
    public function processServerResponse() {
        $_params = $this->getTransactionParams();
        
        $_result = true;

        $_transArr = explode ("::", $_params["codTrans"]);
        $_trans = $_transArr[0];
        
        $this->_debug("Processo l'ordine $_trans server to " . $this->getTransactionType());              
        
        $_order = Mage::getModel('sales/order')->loadByIncrementId($_trans);
        
        if ($_order->getId() > 0) {

            if ( $_params['esito'] == 'OK') {

 
                if (!$_order->getId())
                {
                    $result = false;
                }
                else
                {
                    $_order->getPayment()->setTransactionId($_trans);
                    $this->completeTransaction($_order, array("Session id" => $_params["session_id"],
                                                              "Aut id" => $_params["codAut"],
                                                              "Tans Id" =>  $_params["codTrans"],
                        ));


                }
            }
            else // Esito diverso da OK
            {                
                $this->cancelOrder($_order, Mage::Helper("mpspayment")->__("Errore in fase di pagamento con Autorizzazione:") . $_params["codAut"] . "\n" . "Session:"  . $_params["session_id"]);
                $result = false;            

            }
        }
        
        return $result;
 
    }

}
?>
