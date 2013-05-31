<?php

/**
 *  
 *
 * @category    
 * @package     
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */
class Autel_Corto_Adminhtml_Duty_HarmonizedController extends Mage_Adminhtml_Controller_Action{
    
    public function IndexAction() {
        $this->loadLayout();
        $this->_setActiveMenu('CORTO/get_harmonized_code');
        $this->_addBreadcrumb(Mage::helper('autelcorto')->__('Autel'), Mage::helper('autelcorto')->__('Get Harmonized Code'));
        if (!$this->getRequest()->getQuery('ajax', false))  {
            $this->_addContent( $this->getLayout()->createBlock("autelcorto/adminhtml_duty_harmonized_product"));
            $this->renderLayout();
        }
        else {

            $this->getResponse()->setBody($this->getLayout()->createBlock("autelcorto/adminhtml_duty_harmonized_grid")->toHtml());
        }
        
    }
    
    public function getcodeAction() {
        
        $return = new Varien_Object();
        $return->setStatus('OK');
        $return->setMessage('');
        $return->setHarmonisedCode('');
        
        $helper = Mage::Helper('autelcorto/duty');
        $id = $this->getRequest()->getParam('id');
        $to = $this->getRequest()->getParam('to');

        if (!$helper->getDutyId($id)) {
            $return->setStatus('KO');
            $return->setMessage($helper->__('Il prodotto non ha impostato il Duty Id'));        
        } else {
            $hc = $helper->getCodeFromProduct($id, $to);
            if ($hc !== false) {
                $return->setStatus('OK');
                $return->setHarmonisedCode($hc);
            } else {
                $return->setStatus('KO');
                $return->setMessage($helper->__('Nessun Harmonized Code per il prodotto'));
            }
        }
        
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($return->toJson());
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('CORTO/get_harmonized_code');
    }
}

?>
