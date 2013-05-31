<?php

/**
 * Helper gernico
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */
class Autel_Catalog_Helper_Data extends Mage_Core_Helper_Data 
{
    
    protected $_dbRead;
    protected $_dbWrite;
    protected $_debugEnable;

    public function __construct() {
        $this->_dbRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_dbWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        $this->_debugEnable = Mage::getStoreConfig('autelconnector/connector/debug_enable', 0);
    }
    
    public function getDbReader() {
        return $this->_dbRead;
    }
    public function getDbWriter() {
        return $this->_dbWrite;
    }    

    /**
     * Effettua il log solo se attivo (vedere Mage::Log)
     * @param type $message
     * @param type $level
     * @param type $file
     * @param type $forceLog 
     */
    public function debug($message, $level = null, $file = '', $forceLog = false) {
        if ($this->_debugEnable == 1) {
            if ($file == "") {
                $file = "connector.log";
            }
            Mage::Log($message, $level, $file, $forceLog);
        }
    }
             
    
}

?>
