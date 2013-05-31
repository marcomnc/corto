<?php

class Dutycalculator_Charge_Model_Invoice extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	protected $_code = 'import_duty_tax';

    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$invoice->setImportDutyTax(0);
		$invoice->setBaseImportDutyTax(0);
		$invoice->setDeliveryDutyType($invoice->getOrder()->getDeliveryDutyType());
		$invoice->setFailedCalculation($invoice->getOrder()->getFailedCalculation());
		$invoice->setDcOrderId(0);
		foreach ($invoice->getAllItems() as $invoiceItem)
		{
			$invoiceItem->setImportDutyTax(0);
			$invoiceItem->setBaseImportDutyTax(0);
		}
		if ($invoice->getOrder()->getDcOrderId())
		{
			$result = Dutycalculator_Charge_Model_Importdutytaxes::invoiceCalculation($invoice);
			if ($result)
			{
				$amountToInvoice = $result['total'];
				$invoice->setImportDutyTax($amountToInvoice);
				$invoice->setBaseImportDutyTax($amountToInvoice);
				$invoice->setDeliveryDutyType($invoice->getOrder()->getDeliveryDutyType());
				$invoice->setFailedCalculation($invoice->getOrder()->getFailedCalculation());
				$invoice->setDcOrderId($result['dc_order_id']);
				if ($invoice->getOrder()->getDeliveryDutyType() == Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDP)
				{
					$invoice->setGrandTotal($invoice->getGrandTotal() + $amountToInvoice);
					$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $amountToInvoice);
					$aggregatedItemsValues = array();
					foreach ($invoice->getAllItems() as $invoiceItem)
					{
						if ($invoiceItem->getOrderItem()->getParentItemId())
						{
							continue;
						}
						$id = $invoiceItem->getOrderItem()->getQuoteItemId();
						if (isset($result['items'][$id]))
						{
							$invoiceItem->setImportDutyTax($result['items'][$id]);
							$invoiceItem->setBaseImportDutyTax($result['items'][$id]);
						}
						else
						{
							foreach ($result['aggregated_items'] as $key => $_items)
							{
								if (in_array($id, $_items['items']))
								{
									$aggregatedItemsValues[$key][$id] = $invoiceItem->getRowTotal();
								}
							}
						}
					}
					$taxes = array();
					foreach ($aggregatedItemsValues as $key => $aggregatedItemsValue)
					{
						$aggregatedTax = $result['aggregated_items'][$key]['aggregated_value'];
						$totalAggregatedItemsValue = array_sum($aggregatedItemsValue);
						foreach ($aggregatedItemsValue as $itemId => $value)
						{
							$taxes[$itemId] = round($value / $totalAggregatedItemsValue * $aggregatedTax , 2);
						}
					}
					foreach ($invoice->getAllItems() as $invoiceItem)
					{
						$id = $invoiceItem->getOrderItem()->getQuoteItemId();
						if (isset($taxes[$id]))
						{
							$invoiceItem->setImportDutyTax($taxes[$id]);
							$invoiceItem->setBaseImportDutyTax($taxes[$id]);
						}
						if ($invoiceItem->getQty() == 0)
						{
							$invoiceItem->setImportDutyTax(0);
							$invoiceItem->setBaseImportDutyTax(0);
						}
					}
				}
			}
		}
        return $this;
    }
}
