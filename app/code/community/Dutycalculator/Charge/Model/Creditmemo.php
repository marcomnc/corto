<?php

class Dutycalculator_Charge_Model_Creditmemo extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	protected $_code = 'import_duty_tax';

    public function collect(Mage_Sales_Model_Order_Creditmemo $creditMemo)
    {
		$creditMemo->setImportDutyTax(0);
		$creditMemo->setBaseImportDutyTax(0);
		$creditMemo->setDeliveryDutyType($creditMemo->getOrder()->getDeliveryDutyType());
		$creditMemo->setFailedCalculation($creditMemo->getOrder()->getFailedCalculation());
		$creditMemo->setDcOrderId(0);
		foreach ($creditMemo->getAllItems() as $creditMemoItem)
		{
			$creditMemoItem->setImportDutyTax(0);
			$creditMemoItem->setBaseImportDutyTax(0);
		}
		if ($creditMemo->getOrder()->getDcOrderId())
		{
			$result = Dutycalculator_Charge_Model_Importdutytaxes::creditMemoCalculation($creditMemo);
			if ($result)
			{
				$amountToRefund = $result['total'];
				$creditMemo->setDeliveryDutyType($creditMemo->getOrder()->getDeliveryDutyType());
				$creditMemo->setFailedCalculation($creditMemo->getOrder()->getFailedCalculation());
				$creditMemo->setImportDutyTax($amountToRefund);
				$creditMemo->setBaseImportDutyTax($amountToRefund);
				$creditMemo->setDcOrderId($result['dc_order_id']);
				if ($creditMemo->getOrder()->getDeliveryDutyType() == Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDP)
				{
					$creditMemo->setGrandTotal($creditMemo->getGrandTotal() + $amountToRefund);
					$creditMemo->setBaseGrandTotal($creditMemo->getBaseGrandTotal() + $amountToRefund);
					$aggregatedItemsValues = array();
					foreach ($creditMemo->getAllItems() as $creditMemoItem)
					{
						$id = $creditMemoItem->getOrderItem()->getQuoteItemId();
						if (isset($result['items'][$id]))
						{
							$creditMemoItem->setImportDutyTax($result['items'][$id]);
							$creditMemoItem->setBaseImportDutyTax($result['items'][$id]);
						}
						else
						{
							foreach ($result['aggregated_items'] as $key => $_items)
							{
								if (in_array($id, $_items['items']))
								{
									$aggregatedItemsValues[$key][$id] = $creditMemoItem->getRowTotal();
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
					foreach ($creditMemo->getAllItems() as $creditMemoItem)
					{
						if ($creditMemoItem->getOrderItem()->getParentItemId())
						{
							continue;
						}
						$id = $creditMemoItem->getOrderItem()->getQuoteItemId();
						if (isset($taxes[$id]))
						{
							$creditMemoItem->setImportDutyTax($taxes[$id]);
							$creditMemoItem->setBaseImportDutyTax($taxes[$id]);
						}
						if ($creditMemoItem->getQty() == 0)
						{
							$creditMemoItem->setImportDutyTax(0);
							$creditMemoItem->setBaseImportDutyTax(0);
						}
					}
				}
			}
		}
        return $this;
    }
}
