<?php

/*
 * Estendo la classe base utilizzata per la generazione dei report di vendita in 
 * modo che posso utilizzare il filtro standard
 */

class Autel_Report_Block_Customer_Export_Report extends Mage_Adminhtml_Block_Report_Sales_Sales {
    
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/list', array('_current' => true));
    }
   
}

?>
