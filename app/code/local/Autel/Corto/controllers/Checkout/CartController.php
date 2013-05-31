<?php
// Controllers are not autoloaded so we will have to do it manually:
require_once 'Mage/Checkout/controllers/CartController.php';
class Autel_Corto_Checkout_CartController extends Mage_Checkout_CartController
{       
	public function addAction() {
            
            if ($this->getRequest()->GetParam("ajax")==1) {
                echo "ajax";
                die();
            } else {
                parent::addAction();
            }
        }
}

?>
