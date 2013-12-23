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

    const DEBUG_TYPE_PRODUCT = "prod";
    
    const DEBUG_ACTION_START   = "start";
    const DEBUG_ACTION_UPDATE  = "update";
    const DEBUG_ACTION_END     = "edn";
    
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
     * Effettua il log solo se attivo 
     * @param type $message
     * @param type $type
     */
    public function debug($message, $type=null) {

        if ( $this->_debugEnable) {
            $session = Mage::getSingleton('api/session')->getSessionId();

            $messageLog = Mage::getModel('autelcatalog/message');
            $messageLog->setData('session_id', $session); 
            $messageLog->setData('event_date', date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
            $messageLog->setData('type', (is_null($type)) ? self::DEBUG_TYPE_PRODUCT : $type);
            $messageLog->setData('message', print_r($message, true));

            $messageLog->save();
        }
        
    }
     
    /**
     * Debug imporatazione articolo solo se abilitato
     * @param type $action  Azione START|UPDATE|END
     * @param type $record 
     */
    public function debugImport($action, $record = 1, $type = null ) {
        if ( $this->_debugEnable) {
            $session = Mage::getSingleton('api/session')->getSessionId();
            switch ($action) {
                case self::DEBUG_ACTION_START:
                        $log = Mage::getModel('autelcatalog/import');
                        $log->setData('session_id', $session);
                        $log->setData('start_date', date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
                        $log->setData('type', (is_null($type)) ? self::DEBUG_TYPE_PRODUCT : $type);
                        $log->setData('tot_record', $record);
                        $log->save();
                    break;
                case self::DEBUG_ACTION_END:
                        $log = Mage::getModel('autelcatalog/import')->Load($session, 'session_id');
                        if (!is_null($log)) {
                            $log->setData('end_date', date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
                            $log->save();
                        }
                    break;
                default:
                        $log = Mage::getModel('autelcatalog/import')->Load($session, 'session_id');
                        if (!is_null($log)) {
                            $actElab = $log->getData('elab_record') + 0;
                            $log->setData('elab_record', ($actElab + $record));
                            $log->setData('last_update', date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
                            $log->save();
                        }

                    break;

            }
        }
    }
}

?>
