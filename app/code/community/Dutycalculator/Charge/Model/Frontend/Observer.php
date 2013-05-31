<?php

class Dutycalculator_Charge_Model_Frontend_Observer
{
	public function updatePaypalTotal($evt)
	{
		$cart = $evt->getPaypalCart();
		$salesEntity = $cart->getSalesEntity();
		try
		{
			if (!$salesEntity->getIsVirtual())
			{
				$items = $salesEntity->getAllVisibleItems();
				if (!count($items))
				{
					return $this;
				}
				if ($salesEntity instanceof Mage_Sales_Model_Order)
				{
					$result = Dutycalculator_Charge_Model_Importdutytaxes::getAmount($salesEntity);
				}
				else
				{
					$address = $salesEntity->getShippingAddress();
					if (Dutycalculator_Charge_Model_Importdutytaxes::canApply($address))
					{
						$result = Dutycalculator_Charge_Model_Importdutytaxes::getAmount($salesEntity);
					}
				}

				if ($result['failed_calculation'])
				{
					$salesEntity->setDeliveryDutyType(Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDU);
				}
				else
				{
					$salesEntity->setDeliveryDutyType(Mage::getStoreConfig('dc_charge_extension/dccharge/delivery-type'));
				}
				$balance = $result['total'];
				if ($salesEntity->getDeliveryDutyType() == Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDP)
				{
					$cart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_SHIPPING, $balance);
				}
			}
		}
		catch (Exception $ex)
		{
			$salesEntity->setDeliveryDutyType(Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDU);
		}
	}
}