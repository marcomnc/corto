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

class Autel_Corto_Model_Cms_Block extends Mage_Cms_Model_Block {
    
    public function getUploadDir() {
        return Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'mps' . DS . 'cms' . DS . 'block' . DS . 'background';
    }
    
    public function getBackGroundPath() {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'mps/cms/block/background/';
    }
    
    /**
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave() {
        
        $data = $this->getData();
        if (isset($data['background_image']['delete']) && $data['background_image']['delete'] == 1) {
            // Lo imposto a vuoto, se poi ricarco il file ha la precedenza
            $this->setData('background_image', '');
        }
        
        $_fileName = "";
        if (isset($_FILES['background_image']['name']) && $_FILES['background_image']['name'] != "") {
            
            $uploader = new Varien_File_Uploader("background_image");

            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);

            $result = $uploader->save($this->getUploadDir(), $_FILES['background_image']['name'] );
                        
            $_fileName = $result['file'];
        
        }
        if ($_fileName != "") {
            $this->setData('background_image', $_fileName);
        }       
        
        return parent::_beforeSave();
    }
    
    public function getBackgroundImageUrl() {
        if ($this->getBackgroundImage())
            return $this->getBackGroundPath() . $this->getBackgroundImage();
        else 
            return  '';
    }
}

?>
