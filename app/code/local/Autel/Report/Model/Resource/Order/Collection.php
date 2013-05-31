<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author Doctor
 */
class Autel_Report_Model_Resource_Order_Collection extends Mage_Reports_Model_Resource_Order_Collection {
    
    
    /**
     * Override della funzoine base perchè il venduto va calcolato sull'ordinato e non sul fatturato
     *
     * @param int $isFilter
     * @return Mage_Reports_Model_Resource_Order_Collection
     */
    public function calculateSales($isFilter = 0)
    {
        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = array(0);
        }
        $adapter = $this->getConnection();

        if (Mage::getStoreConfig('sales/dashboard/use_aggregated_data')) {
            $this->setMainTable('sales/order_aggregated_created');
            $this->removeAllFieldsFromSelect();
            $averageExpr = $adapter->getCheckSql(
                'SUM(main_table.orders_count) > 0',
                'SUM(main_table.total_revenue_amount)/SUM(main_table.orders_count)',
                0);
            $this->getSelect()->columns(array(
                'lifetime' => 'SUM(main_table.total_revenue_amount)',
                'average'  => $averageExpr
            ));

            if (!$isFilter) {
                $this->addFieldToFilter('store_id',
                    array('eq' => Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId())
                );
            }
            $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);
        } else {
            $this->setMainTable('sales/order');
            $this->removeAllFieldsFromSelect();

            $expr = sprintf('%s - %s + %s - (%s - %s - %s)',
                $adapter->getIfNullSql('main_table.base_Subtotal_incl_tax', 0),
                $adapter->getIfNullSql('main_table.base_shipping_incl_tax', 0),
                $adapter->getIfNullSql('main_table.base_discount_amount', 0),
                $adapter->getIfNullSql('main_table.base_total_refunded', 0),
                $adapter->getIfNullSql('main_table.base_tax_refunded', 0),
                $adapter->getIfNullSql('main_table.base_shipping_refunded', 0)
            );

            if ($isFilter == 0) {
                $expr = '(' . $expr . ') * main_table.base_to_global_rate';
            }

            $this->getSelect()
                ->columns(array(
                    'lifetime' => "SUM({$expr})",
                    'average'  => "AVG({$expr})"
                ))
                ->where('main_table.status NOT IN(?)', $statuses)
                ->where('main_table.state NOT IN(?)', array(
                    Mage_Sales_Model_Order::STATE_NEW,
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT)
                );
        }
        return $this;
    }

}

?>
