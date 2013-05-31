<?php


/**
 * Backend model for shipping table rates CSV importing
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Marco Mancinelli
 */

class Autel_Shipping_Model_Adminhtml_System_Config_Backend_Shipping_TablerateTNT extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
        $_resource = Mage::getResourceModel('autelshipping/carrier_auteltablerate');
        $_resource->setCarrierCode("TNT");
        $_resource->uploadAndImport($this);
    }
}
?>