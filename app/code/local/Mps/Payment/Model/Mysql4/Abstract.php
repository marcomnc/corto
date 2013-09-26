<?php
/**
 *
 * Description of Abstract
 * 
 * @category    Payment
 * @package     MPS_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */
abstract class Mps_Paymnet_Model_Mysql4_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Prepare data for save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && $object->hasCreatedAt() && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        if ($object->hasUpdateAt()) {
            $object->setUpdatedAt($currentTime);
        }
        $data = parent::_prepareDataForSave($object);
        return $data;
    }

}
?>
