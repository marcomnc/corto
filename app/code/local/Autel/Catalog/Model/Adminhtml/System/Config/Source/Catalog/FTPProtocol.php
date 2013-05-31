<?php

/**
 * Catalog product List of compatible protocols
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Model_Adminhtml_System_Config_Source_Catalog_FTPProtocol {
    
    public function toOptionArray() {
        //Lista attributi
        $ret = array();
        $ret[] = array('value'=>"FTP", 'label'=>"FTP");
        $ret[] = array('value'=>"SFTP", 'label'=>"SFTP");
        return $ret;
    }
    
}

?>
