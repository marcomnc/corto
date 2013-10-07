<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Coloroptions
 *
 * @author doctor
 */
class  Autel_Corto_Model_Coloroptions extends Mage_Core_Model_Abstract
{
    
    protected function _construct()
    {        
        $this->_init('autelcorto/coloroptions');
    }
    
    public function getId() {
        return $this->getEntityId();
    }
    
    public function getUploadDir() {
        return Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'mps' . DS . 'colormanager' . DS;
    }
    
    /**
     * Data un id opzione di ritorna il percorso dell'immagine relativa
     * @param type $optionId
     * @return string
     */
    public function getImageColorUrl()
    {
        $uploadDir = $this->getUploadDir();
        if ($this->getImgUrl() != "" && file_exists($uploadDir . $this->getImgUrl()))
        {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)  . 'mps/colormanager/' . $this->getImgUrl() ;
        }
        return '';
    }
    
    /**
     * Cancello il file immagine selezionato
     * @param type $filename
     */
    public function deleteImageFile() {
        if ($this->getImgUrl()) {
            $file = $this->getUploadDir() . $this->getImgUrl();
            if (file_exists($file)) {
                if (!unlink($file)) {
                    Mage::throwException($this->__('errore in fase di cancellazione di') . " $file");
                }
            }
        }
    }
    
    public function getImageUrl() {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)  . 'mps/colormanager/';
    }

}

?>
