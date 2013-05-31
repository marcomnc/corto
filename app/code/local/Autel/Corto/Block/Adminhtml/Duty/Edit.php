<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * Description of Edit
 * 
 * @category    default
 * @package     
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     30-ago-2012
 */
class Autel_Corto_Block_Adminhtml_Duty_Edit extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('autel/corto/duty.phtml');
    }

    public function getHeaderText()
    {
        return Mage::helper('autelcorto')->__('Duty Calculator lookup HS Code');
    }
}


?>
