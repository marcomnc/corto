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
class MpsSistemi_Iplocation_Adminhtml_ZoneController extends Mage_Adminhtml_Controller_Action 
{     

    
    private function _setTitle() {
        $this->_title($this->__('MPS'))->_title($this->__('Gestione Zone'));        
        $this->loadLayout()
            ->_setActiveMenu('MPS')
            ->_addBreadcrumb(Mage::helper('mpslocation')->__('MPS'),
                             Mage::helper('mpslocation')->__('Gestione Zone'));     
        return $this;
    }
    
    public function indexAction() {
        $this->_setTitle();   
        
//        Se si implementa un parametro di controllo
//        if (mage::getStoreConfig('path configurazine /enabled') != 1) {
//            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mpspricezone')->__('Modulo non abiltato o non correttamente configurato!'));
//            $this->_redirect('*/*/');
//        } else {
            // load layout, set active menu and breadcrumbs
            $priceZoneBlock = $this->getLayout()->createBlock('mpslocation/adminhtml_zone_zone');
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
        $model  = Mage::getModel('mpslocation/zone');

        if ($id) {  
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mpslocation')->__('La zona selezionata non esiste'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        $this->_title($model->getId() ? $model->getDescription() : $this->__('Nuova Zona'));
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);        
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('mpslocation_zone', $model);
        

        $this->_addContent($this->getLayout()->createBlock('mpslocation/adminhtml_zone_edit'))
             ->_addLeft($this->getLayout()->createBlock('mpslocation/adminhtml_zone_edit_tabs'));

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
        $errorNo = true;
        $e = "";
        
        //Controllo che ci sia il website
        if ($data['website_id'] == "") {
            $errorNo = false;
            $e = "Web site non selezionato";
        }
        
        //Verifico se a lingua è coerente
        if ($errorNo)
            foreach (Mage::app()->getStores() as $store) {
                if (array_search($store->getId(), $data['store_id']) !== false && $store->getWebsiteId() != $data["website_id"][0]) {
                    $errorNo = false;
                    $e .= $store->getName() . " Non appartiene al Web Site Selezionato<br>";
                }
            }
        if ($errorNo && 1==0) //Lo faccio in fase di refresh dei dati 
            foreach ($data['state_list'] as $state) {
                $store = Mage::Helper('mpslocation')->getStoreFromState($state);   
                if (is_null($store)) {
                    $errorNo = false;
                    $e .= "Stato " . Mage::getModel('directory/country')->load($state)->getName() ." non assegnato a nessun Website<br>";
                }elseif ($data['website_id'][0] != $store->getWebsiteId()) {
                    $errorNo = false;
                    $e .= "Stato " .  $state . "-" . Mage::getModel('directory/country')->load($state)->getName() ." appartiene al Web Site " ;
                    $e .= Mage::getModel('core/website')->Load($store->getWebsiteId())->getName()."<br>";
                }
            }
            
            
        if (!$errorNo) {
            Mage::getSingleton('adminhtml/session')->addError($e);
        }
       
        return $errorNo;
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
            $model = Mage::getModel('mpslocation/zone');

            if ($id = $this->getRequest()->getParam('entity_id')) {
                $model->load($id);
            }

            $model->setData($data);

            //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('entity_id' => $this->getRequest()->getParam('entity_id')));
                //$this->_redirect('*/*/');
                return;
            }

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mpslocation')->__('The page has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // go to grid
                if ($data['refresh_config'] == 1) {
                    $this->_refresh();
                }
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('mpslocation')->__('An error occurred while saving the page.'));
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
            $zone = Mage::getModel('mpslocation/zone')->load($id);
            $description = $zone->getDescription();
            try {
                $zone->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Zone '. $description . ' has been deleted.'));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::LogException($e);
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
        $grid          = $this->getLayout()->createBlock('mpslocation/adminhtml_zone_grid');
        
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

    public function masschangegroupAction() {
        
        $entities = $this->getRequest()->getParam('entity_id', array());
        $groupId = $this->getRequest()->getParam('group_id', 0);

        if (is_array($entities) && $groupId > 0 ) {
            if (sizeof($entities) == 0) {
                $this->_getSession()->addError(
                    Mage::helper('mpslocation')->__('Nessuna Zona selezionata'));
            } else {
                $group = Mage::getModel('mpslocation/zonegroup')->Load($groupId);
                if (!is_null($group)) {
                    try {
                        foreach ($entities as $entity) {
                            $zone = Mage::getModel('mpslocation/zone')->Load($entity);
                            if (!is_null($zone)) {
                                $zone->setGroupId($groupId);
                                $zone->save();
                            }
                            
                        }
                        $this->_getSession()->addSuccess(
                                        Mage::Helper('mpslocation')->__('Zone Aggiornate correttametne'));
                    } catch (Exception $e) {
                        $this->_getSession()->addError( $e->getMessage());
                        Mage::LogException($e);
                    }

                } else {
                    $this->_getSession()->addError(
                        Mage::helper('mpslocation')->__('Il gruppo selezionato non esiste'));
                }
            }
        }        
        
        $this->_redirect('*/*/index');
        
    }
    
    /**
     * Eseguo l'aggiornamento della configurazione degli store in base alle zone.
     * Lo faccio sempre in Maniera globale
     */
    public function refreshAction() {
        
        $toEdit = $this->getRequest()->getParam('entity_id',0);
        $this->_refresh();
        $this->_redirect(($toEdit > 0) ? '*/*/edit' : '*/*/index');
    }
    
    private function _refresh() {
        
        if (Mage::Helper('mpslocation')->checkZone()) {
            try {
                Mage::Helper('mpslocation')->refreshZone();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::Helper('mpslocation')->__('Configurazione paesi ricalcolata. AZZERARE LA CACHE!'));
            } catch (Exception $e) {
                $this->_getSession()->addError( $e->getMessage());
                        Mage::LogException($e);
            }
        }
    }
}

?>
