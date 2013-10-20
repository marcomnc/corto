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
class Autel_Corto_Model_Adminhtml_Observer {
    
    /**
     * Salvataggio 
     * @param type $observer
     */
    public function on_attribute_after_save($observer) {
        
       $data = $observer->getAttribute()->getData();

        foreach ($data['option']['value'] as $value => $val) {
            
            if (!is_numeric($value)) {
                //new record
                continue;
            }

            //Controllo se il recor è stato cancellato            
            if (isset($data['option']['delete'][$value]) && $data['option']['delete'][$value] == 1) {
                $model = Mage::getModel('autelcorto/coloroptions')->Load($value, 'option_id');
                if ($model->getId() > 0) {
                    $model->delete();
                }
            } else {
                
                if (isset($_FILES['filename']['name'][$value]) && $_FILES['filename']['name'][$value] != "") {
                    try {
                        $_fileName = "";
                        $uploader = new Varien_File_Uploader("filename[$value]");

                        $uploader->setAllowedExtensions(array('jpg'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);

                        $result = $uploader->save(Mage::getModel('autelcorto/coloroptions')->getUploadDir(), $_FILES['filename']['name'][$value] );
                        
                        $_fileName = $result['file'];

                    } catch (Exception $ex) {
                        Mage::getSingleton('adminhtml/session')
                                ->addError(Mage::helper('autelcorto')->__(
                                        'Errore in fase di Memorizzazione del file ' . $_FILES['filename']['name'][$value]) ."\n" . 
                                        $ex->getMessage());
                    }
                }
                if (isset($data['filename_delete'][$value]) && $data['filename_delete'][$value] = "1") {
                    $model = Mage::getModel('autelcorto/coloroptions')->Load($value, 'option_id');
                    $model->deleteImageFile();
                    $_fileName = "";
                }
                
                $model = Mage::getModel('autelcorto/coloroptions')->Load($value, 'option_id');
                if ($model->getId() == 0) {
                    $model = Mage::getModel('autelcorto/coloroptions');
                    $model->setData('option_id', $value);
                }
                $model->setData('attribute_id', $data['attribute_id']);
                if (isset($_fileName)) 
                    $model->setData('img_url', $_fileName);
                $model->setData('color_hex', isset($data['color_hex'][$value]) ? $data['color_hex'][$value]: '');             
                $model->Save();
            }
        }
                
    }
    
    /**
     * Preparazione dati per il salvataggio della pagina cms
     * @param type $observer
     */
    public function on_page_prepare_save ($observer) {
        $page = $observer->getPage();
        $request = $observer->getRequest();
        
        $page->setBlockList($request->getPost('block_list'));
        
        return $observer;
    }
    
    /**
     * Salvataggio della pagina CMS
     * @param type $observer
     */
    public function on_page_after_save ($observer) {
        $page = $observer->getObject();
        
        //Cancello e reinserisco sempre
        
        $pageblocks = Mage::getModel('autelcorto/cms_pageblocks');
        $blocks = preg_replace("/%3d/i", "", $page->getBlockList());

        //$transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            // Non se pò fa
            //$transaction->beginTransaction();
            
            $pageblocks->deleteForPage($page->getId());
            
            foreach (preg_split("/&/", $blocks) as $value64) {
                if (preg_match("/=/", $value64)) {
                    $value64 = preg_split("/=/", $value64);                    
//                    $value64[0] = Id del blocco
//                    $value64[1] = Campi decodificati                     
                    $fields = preg_split("/&/", base64_decode($value64[1]));
                    
                    $pageblocks = Mage::getModel('autelcorto/cms_pageblocks');
                    $pageblocks->setData('page_id', $page->getId());
                    $pageblocks->setData('block_id', $value64[0]);
                    foreach ($fields as $field) {
                        $keyValue = preg_split("/=/", $field); 
                        $pageblocks->setData($keyValue[0], urldecode($keyValue[1]));
                    }

                    $pageblocks->save();
                }
            }

            //$transaction->commit();
        } catch (Exception $e) {
            //$transaction->rollback();            
            Mage::getSingleton('adminhtml/session')->addWarning(
                    Mage::helper('autelcorto')->__('Si sono verificati errori in fase di memorizzazione della squenza blocchi')
            );
        }        
        
        return $observer;
    }
    
    
}
?>