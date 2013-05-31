<?php

class Dutycalculator_Charge_Block_Sales_Order_Total extends Mage_Core_Block_Template
{
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize reward points totals
     *
     * @return Enterprise_Reward_Block_Sales_Order_Total
     */
    public function initTotals()
    {
		try
		{
		   	$source = $this->getSource();
            $value  = $source->getImportDutyTax();
			$title = ($this->getOrder()->getDeliveryDutyType() == Dutycalculator_Charge_Helper_Data::DC_DELIVERY_TYPE_DDU) ? ($this->getOrder()->getFailedCalculation() ? 'Any import duty & taxes are paid upon delivery and are not included in the final price' : 'Estimated import duty & taxes (Not included in grand total, paid upon delivery)') : 'Import duty and taxes';
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'import_duty_tax',
                'strong' => false,
                //'label'  => Mage::helper('import_duty_taxes')->formatFee($value),
                'label' => $title,
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }
		catch (Exception $ex)
		{

		}

        return $this;
    }
}
