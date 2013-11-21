<?php
/**
 * Delivery Date and Customer Comment - Magento Extension
 *
 * @package     Ddc
 * @category    EcommerceTeam
 * @copyright   Copyright 2011 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    2.1.0
 */

class EcommerceTeam_Ddc_Model_Observer
{
    /**
     * @param $event
     * @return void
     */
    public function coreBlockAbstractPrepareLayoutBefore(Varien_Event_Observer $event)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $event->getData('block');
        switch($block->getData('type')):
            case ('adminhtml/sales_order_grid'):
                /** @var $block Mage_Adminhtml_Block_Sales_Order_Grid */
                if (Mage::getVersion() < '1.4.0') {
                    $block->addColumn('delivery_date', array(
                        'header' => Mage::helper('sales')->__('Delivery Date'),
                        'type' => 'date',
                        'index' => 'delivery_date',
                        'width' => '160px',
                    ));
                } else {
                    $block->addColumnAfter('delivery_date', array(
                    'header' => Mage::helper('sales')->__('Delivery Date'),
                    'type' => 'date',
                    'index' => 'delivery_date',
                    'width' => '160px',
                    ), 'created_at');
                }
            break;
        endswitch;
    }

    /**
     * @param $event
     * @return void
     */
    public function loadOrderData(Varien_Event_Observer $event)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $event->getData('order');
        /** @var $orderData EcommerceTeam_Ddc_Model_Order */
        $orderData = Mage::getModel('ecommerceteam_ddc/order')->load($order->getEntityId(), 'order_id');
        $data      = $orderData->getData();

        if (isset($data['order_id'])) {
            unset($data['entity_id'], $data['order_id']);
            if (strtotime($data['delivery_date'])) {
                $formattedDate = Mage::getSingleton('core/locale')->date($data['delivery_date'], Zend_Date::ISO_8601, null, false)->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL));
                $data['delivery_date_formatted'] = $formattedDate;
            } else {
                unset($data['delivery_date']);
            }

            $order->addData($data);
        }
    }

    /**
     * @param $event
     * @return void
     */
    public function loadQuoteData(Varien_Event_Observer $event)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $event->getData('quote');
        $data = Mage::getModel('ecommerceteam_ddc/quote')->load($quote->getId(), 'quote_id')->getData();
        if (isset($data['quote_id'])) {
            unset($data['entity_id'], $data['quote_id']);
            $quote->addData($data);
        }
    }

    /**
     * @param Varien_Event_Observer $event
     */
    public function saveOrderData(Varien_Event_Observer $event)
    {
        /** @var $debugHelper EcommerceTeam_Core_Helper_Debug */
        $debugHelper = Mage::helper('ecommerceteam_core/debug');
        /** @var $helper EcommerceTeam_Ddc_Helper_Data*/
        $helper = Mage::helper('ecommerceteam_ddc');
        /** @var $order Mage_Sales_Model_Order */
        $order   = $event->getData('order');
        /** @var $quote Mage_Sales_Model_Quote */
        $quote   = $event->getData('quote');

        try {
            $deliveryDate = $quote->getData('delivery_date');
            if (!$deliveryDate) {
                $date      = Mage::app()->getLocale()->date();
                $date->setDay($date->toString('d')+intval($helper->getConfigData('min_day')));
                $deliveryDate = strtotime(Date('Ymd', $date->getTimeStamp()+$date->getGmtOffset()));
            }

            /** @var $dataModel EcommerceTeam_Ddc_Model_Order */
            $dataModel = Mage::getModel('ecommerceteam_ddc/order');
            $dataModel->addData(
                array (
                    'order_id'         => $order->getEntityId(),
                    'delivery_date'    => $deliveryDate,
                    'customer_comment' => $quote->getData('customer_comment'),
                )
            );
            $dataModel->save();
        } catch (Exception $e) {
                //continue
        }
    }

    /**
     * @param Varien_Event_Observer $event
     */
    public function saveQuoteData(Varien_Event_Observer $event)
    {
        /** @var $debugHelper EcommerceTeam_Core_Helper_Debug */
        $debugHelper = Mage::helper('ecommerceteam_core/debug');
        /** @var $helper EcommerceTeam_Ddc_Helper_Data */
        $helper    = Mage::helper('ecommerceteam_ddc');
        /** @var $quote Mage_Sales_Model_Quote */
        $quote     = $event->getData('quote');
        /** @var $dataModel EcommerceTeam_Ddc_Model_Quote */
        $dataModel = Mage::getModel('ecommerceteam_ddc/quote');
        $dataModel->load($quote->getId(), 'quote_id');

        if (!$dataModel->getId()) {
            $dataModel->setData('quote_id', $quote->getId());
        }

        try {
            $dataModel->setData('delivery_date',    $quote->getData('delivery_date'));
            $dataModel->setData('customer_comment', $quote->getData('customer_comment'));
            $dataModel->save();
        } catch (Exception $e) {
            //continue
        }

    }

    /**
     * Initialize request data and set to quote.
     *
     * @param Varien_Event_Observer $event
     */
    public function initRequest(Varien_Event_Observer $event)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote     = $event->getData('quote');
        $request   = Mage::app()->getRequest();

        if ($deliveryDate = $request->getParam('delivery_date')) {
            $quote->setData('delivery_date', strtotime(Date('Ymd', strtotime($deliveryDate))));
        }
        if ($customerComment = trim($request->getParam('customer_comment'))) {
            $quote->setData('customer_comment', $customerComment);
        }
    }
}
