<?php

/**
 *
 * Lingue permesse per il metodo di pagamento Consorzio Triveneto
 * 
 * @category    default
 * @package     
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     22-ago-2012
 */
class Mps_Payment_Model_Adminhtml_System_Config_Source_Payment_Language_Triveneto
{
    private $_lang = array("ITA" => "italiano", 
                           "USA" => "inglese" ,
                           "FRA" => "francese",
                           "DEU" => "Tedesco" ,
                           "ESP" => "spagnolo",
                           "SLO" => "sloveno" );
    
    public function toOptionArray() 
    {
        $options = array();
        $options[] = array(
               'value' => '',
               'label' => Mage::helper('mpspayment')->__('-- No language specify --')
            );
        foreach ($this->_lang as $code=>$label) {
            $options[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        return $options;        
    }

    
}

?>
