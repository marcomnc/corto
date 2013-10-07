<?php
class Autel_Corto_SearchController extends Mage_Core_Controller_Front_Action
{
    public function colorAction() {	

        $this->loadLayout();
        $this->getLayout()->getBlock("cortoSearchByColor");            
        $this->renderLayout();            
    }
    
}

?>
