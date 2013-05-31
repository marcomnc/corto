<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * Description of DutyController
 * 
 * @category    default
 * @package     
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     30-ago-2012
 */
class Autel_Corto_Adminhtml_DutyController extends Mage_Adminhtml_Controller_Action{
    
    public function IndexAction() {
        $this->loadLayout();
        $this->_setActiveMenu('CORTO/duty_classification');
        $this->_addBreadcrumb(Mage::helper('autelcorto')->__('Aulte'), Mage::helper('autelcorto')->__('Duty Calculator'));
        $this->_addContent( $this->getLayout()->createBlock("autelcorto/adminhtml_duty_edit"));
        $this->renderLayout();
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('CORTO/duty_classification');
    }
}

?>
