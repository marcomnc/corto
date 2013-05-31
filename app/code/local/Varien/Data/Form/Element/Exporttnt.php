<?php

/**
 * Export CSV button for shipping table rates
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Exporttnt extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website'),
            'carrier' => 'TNT'
        );

        $data = array(
            'label'     => Mage::helper('adminhtml')->__('Export CSV'),
            'onclick'   => 'setLocation(\''.Mage::helper('adminhtml')->getUrl("autelshipping/adminhtml_shipping/exporttablerates/", $params) . 'conditionName/\' + $(\'carriers_tablerate_condition_name\').value + \'/tablerates.csv\' )',
            'class'     => '',
        );

        $html = $buttonBlock->setData($data)->toHtml();

        return $html;
    }
}
