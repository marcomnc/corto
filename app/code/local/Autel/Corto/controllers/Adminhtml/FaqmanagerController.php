<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZoneController
 *
 * @author marcoma
 */
class Autel_Corto_Adminhtml_FaqmanagerController extends Mage_Adminhtml_Controller_Action 
{     

    
    private function _setTitle() {
        $this->_title($this->__('Autel Corto'))->_title($this->__('Gestione FAQ'));
        $this->loadLayout()
            ->_setActiveMenu('Corto')
            ->_addBreadcrumb(Mage::helper('autelcorto')->__('Autel Corto'),
                             Mage::helper('autelcorto')->__('Gestione FAQ'));     
        return $this;
    }
    
    public function indexAction() {
        $this->_setTitle();   
        
            $priceZoneBlock = $this->getLayout()->createBlock('autelcorto/adminhtml_cms_faq_faq');
            $this->_addContent($priceZoneBlock);
            
            $this->renderLayout();
//        }
    }      
    
    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $this->_setTitle();  
        
        $id     = $this->getRequest()->getParam('entity_id');
        $model  = Mage::getModel('autelcorto/faq');

        if ($id) {  
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autelcorto')->__('Il gruppo FAQ Selezionato non esiste'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('Nuovo gruppo FAQ'));
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);        
        if (! empty($data)) {
            $model->setData($data);
        }
        Mage::register('autelcorto_faq', $model);
        

        $this->_addContent($this->getLayout()->createBlock('autelcorto/adminhtml_cms_faq_edit'));

        $this->renderLayout();

    }
    
    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    protected function _validatePostData($data)
    {
        return true;
    }
    
    
    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // Da utilizare per eventuali conversioni valuta date ecc....
            //$data = $this->_filterPostData($data);
            //init model and set data
            $model = Mage::getModel('autelcorto/faq');

            if ($id = $this->getRequest()->getParam('entity_id')) {
                $model->load($id);
            }

            //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('entity_id' => $this->getRequest()->getParam('entity_id')));
                //$this->_redirect('*/*/');
                return;
            }

            if (!isset($data['stores']) || $data['stores'].'' == '') {
                $data['stores'] = array (0 => Mage_Core_Model_App::ADMIN_STORE_ID);
            }

            if (isset($data['faq_serial']) && is_array($data['faq_serial'])) {
                $serial = $data['faq_serial'];            
                unset($serial['__empty']);
                $data['faq_serial'] = $serial;
            }

            $model->setData($data);
            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('autelcorto')->__('The page has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('autelcorto')->__('An error occurred while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('entity_id' => $this->getRequest()->getParam('entity_id')));
            return;
        }
        $this->_redirect('*/*/');
    }

    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('entity_id')) {
            $zone = Mage::getModel('autelcorto/zone')->load($id);
            $description = $zone->getDescription();
            try {
                $zone->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Zone '. $description . ' has been deleted.'));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }    
    
    /**
     * Export grid to CSV/XML format
     */
    public function exportAction()
    {
        $typeExport    = $this->getRequest()->getParam("exp_type");
        $fileName      = 'Size_request.' . $typeExport;
        $grid          = $this->getLayout()->createBlock('autelcorto/adminhtml_zone_grid');
        
        switch ($typeExport) {
            case 'csv':
                    $gridResultSet = $grid->getCsvFile();
                break;
            default:
                    $gridResultSet = $grid->getExcelFile($fileName);
                break;
        }
        $this->_prepareDownloadResponse($fileName, $gridResultSet);
    }     

}

?>
