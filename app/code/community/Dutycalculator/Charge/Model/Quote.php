<?php

class Dutycalculator_Charge_Model_Quote extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected $_code = 'import_duty_tax';

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		$this->_setAddress($address);
		if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
		{
			$quote = $address->getQuote();

			$address->setImportDutyTax(0);
			$address->setBaseImportDutyTax(0);

			$quote->setImportDutyTax(0);
			$quote->setBaseImportDutyTax(0);
			$quote->setDeliveryDutyType(Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDU);
			$quote->setFailedCalculation(0);
			$quote->setDcOrderId(0);

			$items = $quote->getAllVisibleItems();
			if (!count($items))
			{
				return $this; //this makes only address type shipping to come through
			}
			foreach ($items as $quoteItem)
			{
				$quoteItem->setImportDutyTax(0);
				$quoteItem->setBaseImportDutyTax(0);
			}
			if (Dutycalculator_Charge_Model_Importdutytaxes::canApply($address) && !$quote->getIsVirtual())
			{
				$result = Dutycalculator_Charge_Model_Importdutytaxes::getAmount($quote);
				if (is_array($result))
				{
					$balance = $result['total'];
					$address->setImportDutyTax($balance);
					$address->setBaseImportDutyTax($balance);

					$quote->setImportDutyTax($balance);
					$quote->setBaseImportDutyTax($balance);

					if ($result['failed_calculation'])
					{
						$quote->setDeliveryDutyType(Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDU);
					}
					else
					{
						$quote->setDeliveryDutyType(Mage::getStoreConfig('dc_charge_extension/dccharge/delivery-type'));
					}
					$quote->setFailedCalculation($result['failed_calculation']);
					$quote->setDcOrderId($result['dc_order_id']);
					if ($quote->getDeliveryDutyType() == Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDP)
					{
						$aggregatedItemsValues = array();
						foreach ($items as $quoteItem)
						{
							if (isset($result['items'][$quoteItem->getId()]))
							{
								$quoteItem->setImportDutyTax($result['items'][$quoteItem->getId()]);
								$quoteItem->setBaseImportDutyTax($result['items'][$quoteItem->getId()]);
							}
							else
							{
								foreach ($result['aggregated_items'] as $key => $_items)
								{
									if (in_array($quoteItem->getId(), $_items['items']))
									{
										$aggregatedItemsValues[$key][$quoteItem->getId()] = $quoteItem->getRowTotal();
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
						foreach ($items as $quoteItem)
						{
							if (isset($taxes[$quoteItem->getId()]))
							{
								$quoteItem->setImportDutyTax($taxes[$quoteItem->getId()]);
								$quoteItem->setBaseImportDutyTax($taxes[$quoteItem->getId()]);
							}
						}
						$address->setGrandTotal($address->getGrandTotal() + $address->getImportDutyTax());
						$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseImportDutyTax());
					}
				}
			}
		}
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
		{
			if (Dutycalculator_Charge_Model_Importdutytaxes::canApply($address))
			{
				$amt = $address->getImportDutyTax();
				$title = ($address->getQuote()->getDeliveryDutyType() == Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDU) ? ($address->getQuote()->getFailedCalculation() ? 'Any import duty & taxes are paid upon delivery and are not included in the final price' : 'Estimated import duty & taxes (Not included in grand total, paid upon delivery)') : 'Import duty and taxes';
				$address->addTotal(array(
										'code' => $this->getCode(),
										'title'=> $title,
										'value'=> $amt
								   ));
			}
		}
		return $this;
	}
}
