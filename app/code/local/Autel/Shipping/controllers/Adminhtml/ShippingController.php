<?php

/**
 * Admin autel shipping controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Marco Mancinelli
 */
class Autel_Shipping_Adminhtml_ShippingController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction() {
        echo "Index";
        die();
    }
    /**
     * Export shipping table rates in csv format
     *
     */
    public function exportTableratesAction()
    {
        $fileName   = 'tablerates.csv';
        /** @var $gridBlock Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid */
        $gridBlock  = $this->getLayout()->createBlock('autelshipping/adminhtml_shipping_carrier_tablerate_grid');
        $website    = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
        $carrierCode= $this->getRequest()->getParam('carrier');
        $fileName   = $carrierCode . "_" . $fileName;
        //if ($this->getRequest()->getParam('conditionName')) {
        //    $conditionName = $this->getRequest()->getParam('conditionName');
        //} else {
        //    $conditionName = $website->getConfig('carriers/tablerate/condition_name');
        //}
        $conditionName = Autel_Shipping_Model_Carrier_Auteltablerate::SHIPPING_CONDITION;
        $gridBlock->setWebsiteId($website->getId())
                  ->setConditionName($conditionName)
                  ->setCarrireCode($carrierCode);
        $content    = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}

?>
