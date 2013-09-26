<?php
/**
 *
 * Crea la form di redirect che inoltra le richieste alla banca
 * il tutto si basa sul metodo getFieldPost che ritorna i campi necessari da inviare 
 * alla banca in post. Ogni metodo di pagamento esporra il suo metodo custom per la creazione dei campi
 * 
 * Questo blocco va bene per tutti quelli che accettano i paramentri in post
 * 
 * @category    default
 * @package     
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     23-ago-2012
 */
class Mps_Payment_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected $_method = null;
    /**
     * Recupero il model associato al pagmento (può essere utile in caso di extends)
     * @return type 
     */
    protected function _getPaymentModule() {
        $this->_method = $this->getRequest()->getParam("_method"); 
        return Mage::getModel(Mage::getStoreConfig("payment/" . $this->_method . '/model'));
    }
    
    protected function _toHtml() {
        
        $_payModel = $this->_getPaymentModule();
        $_form = new Varien_Data_Form();
        $_form->setAction($_payModel->getGatewayUrl())
            ->setId($this->_method)
            ->setName($this->_method)
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($_payModel->getPaymentFormFields() as $field=>$value) {
            $_form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        
        $_curtesyMessage = "You will be redirected to " . Mage::getStoreConfig("payment/" . $this->_method . "/title") . "in a few seconds".
        
        $html = '<html><body>';
        $html.= '<a href="javascript:gosub();">' . $this->__($_curtesyMessage) . '</a>';
        $html.= $_form->toHtml();
        $html.= '<script type="text/javascript">
        
        function gosub() {
	        document.getElementById("' . $this->_method .'").submit();	
        }
        document.getElementById("' . $this->_method . '").submit();
        
        </script>';
        $html.= '</body></html>';

        return $html;
    }
}

?>
