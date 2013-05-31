<?php

/**
 *
 * Description of DutyController
 * 
 * @category    Report
 * @package     
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     29-Mar-2012
 */

class Autel_Report_CustomerController extends Mage_Adminhtml_Controller_Action{
    
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Customers'), Mage::helper('reports')->__('Customers'));
        return $this;
    }
    
    public function listAction() {
        $this->_title($this->__('Custom Inquiry'))->_title($this->__('Export Customer'));
        
        $this->_initAction()
            ->_setActiveMenu('report/customer/accounts')
            ->_addBreadcrumb($this->__('Esportazione utenti'), $this->__('Esportazione utenti'));
        
        $rptBlock = $this->getLayout()->createBlock('autelrpt/customer_export_report','rpt_header')
                         ->setTemplate('report/grid/container.phtml');
        $storeBolck = $this->getLayout()->createBlock('adminhtml/store_switcher', 'store.switcher')
                           ->setTemplate('report/store/switcher/enhanced.phtml');
        $gridBlock = $this->getLayout()->createBlock('autelrpt/customer_export_grid','grid');
        
        // $block->setFilterData($params);
        $this->getLayout()->getBlock('rpt_header')
                          ->append($storeBolck)
                          ->append($gridBlock);
        
        $this->_addContent($rptBlock);
        
        $this->renderLayout();
        
    }
    
    public function exportlistAction () {        
        
        $fileType = $this->getRequest()->getParam("exp_type");
        $fileName = 'CustomerList.' . $fileType;

        if ($fileType == "xls") {
            $content = $this->getLayout()->createBlock('autelrpt/customer_export_grid')
                            ->getExcelFile($fileName);
        } else {
            $content = $this->getLayout()->createBlock('autelrpt/customer_export_grid')
                            ->getCsvFile($fileName);
        }
 
        $this->_prepareDownloadResponse($fileName, $content);
        
    }    
    
    
}

?>
