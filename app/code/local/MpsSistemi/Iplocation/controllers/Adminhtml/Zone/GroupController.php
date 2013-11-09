<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *  
 *
 * @category    
 * @package     
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */
class MpsSistemi_Iplocation_Adminhtml_Zone_GroupController extends Mage_Adminhtml_Controller_Action 
{
    private function _setTitle() {
        $this->_title($this->__('Mps'))->_title($this->__('Gestione Zone'));        
        $this->loadLayout()
            ->_setActiveMenu('MPS')
            ->_addBreadcrumb(Mage::helper('mpslocation')->__('Mps'),
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
            $priceZoneBlock = $this->getLayout()->createBlock('mpslocation/adminhtml_zone_group_group');
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
        $model  = Mage::getModel('mpslocation/zonegroup');

        if ($id) {  
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mpslocation')->__('Il gruppo selezionato non esiste'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        $this->_title($model->getId() ? $model->getGroupName() : $this->__('Nuovo Gruppo'));
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);        
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('mpslocation_zone_group', $model);
        

        $this->_addContent($this->getLayout()->createBlock('mpslocation/adminhtml_zone_group_edit'));

        $this->renderLayout();

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
            $model = Mage::getModel('mpslocation/zonegroup');

            if ($id = $this->getRequest()->getParam('entity_id')) {
                $model->load($id);
            }

            $model->setData($data);

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
            $zone = Mage::getModel('mpslocation/zonegroup')->load($id);
            $name = $zone->getName();
            try {
                $zone->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Group  '. $name . ' has been deleted.'));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }    
    
    
}

?>
