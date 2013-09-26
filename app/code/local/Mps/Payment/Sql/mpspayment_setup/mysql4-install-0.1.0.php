<?php

/**
 *
 * Creazione delle tabelle di log delle transazioni di pagamento
 * 
 * @category    Payment
 * @package     Mps_Payment
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */

$installer = $this;
/** @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'payment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mpspayment/transactionlog'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Primary key')
    ->addColumn('payment_method', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Payment Method') 
    ->addColumn('real_order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        'default'   => '',
        ), 'Incremental_id of the associate order')
    ->addColumn('intrernal_transaction', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable'  => true,
        'default'   => '',
        ), 'Store transaction Id')
    ->addColumn('external_transaction', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable'  => true,
        'default'   => '',
        ), 'Banck transaction Id')
    ->addColumn('transaction_type', Varien_Db_Ddl_Table::TYPE_TEXT, 30, array(
        'nullable'  => true,
        'default'   => '',
        ), 'Type of transaction if required')
    ->addColumn('transaction_status', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => true,
        'default'   => '',
        ), 'Status of the Transaction')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Updated At')
    ->addIndex($installer->getIdxName('mpspayment/transactionlog', 
            array('payment_method', 'intrernal_transaction'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
            array('payment_method', 'intrernal_transaction'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('MPS Payment Methos - Transaction Log');

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('mpspayment/transactionstatus'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Primary key')
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 40, array(
        'nullable'  => false,
        ), 'Associated transaction')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')
    ->addColumn('direction', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        ), 'direction of operation')
    ->addColumn('prev_order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Previus Order Status')
    ->addColumn('act_order_status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Actual Order Status')
    ->addColumn('complex_data', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'data transmitted or received in this operation')
    ->addIndex($installer->getIdxName('mpspayment/transactionstatus', 
        array('transaction_id', 'entity_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('transaction_id', 'entity_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('mpspayment/transactionstatus',array('created_at')),
        array('created_at'))
    ->addForeignKey($installer->getFkName('mpspayment/transactionstatus', 'transaction_id', 'mpspayment/transactionlog', 'entity_id'),
        'transaction_id', $installer->getTable('mpspayment/transactionlog'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('MPS Payment Methos - Transaction Detail Log');

$installer->getConnection()->createTable($table);

$installer->endSetup();
