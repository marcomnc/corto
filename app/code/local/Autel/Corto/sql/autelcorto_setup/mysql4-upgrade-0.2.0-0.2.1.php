<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->getConnection()->addColumn($this->getTable('cms/block'), 'background_image', 
                                        array('TYPE'    => Varien_Db_Ddl_Table::TYPE_TEXT,
                                              'LENGTH'  => '255',
                                              'NULLABLE'=> true,
                                              'COMMENT' => 'Immagine di sfondo'));

$installer->endSetup();

?>
