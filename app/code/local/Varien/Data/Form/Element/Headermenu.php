<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Headermenu
 *
 * @author doctor
 */
class Varien_Data_Form_Element_Headermenu extends Varien_Data_Form_Element_Abstract {
    
    public function getElementHtml()
    {        
        
         $this->setData('delete_button',
            $this->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('autelcorto')->__('Delete'),
                    'class' => 'delete delete-header-menu'
                )));

        $this->setData('add_button',
            $this->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('autelcorto')->__('Add Link'),
                    'class' => 'add',
                    'id'    => 'add_new_header_menu'
                )));
        
        $this->setData('confirm_button',
            $this->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('autelcorto')->__('Confirm'),
                    'class' => 'save',
                    'id'    => 'save_link'
                )));
        
        
        
        ob_start();
        include __DIR__ . "/headermenu.phtml";
        $html = ob_get_clean();
        return $html;
    }
    
    protected function _getHeaderData() {
        
        $data = array();
        if ($this->getValue() != "") {
            $i=0;
            $rows = preg_split("/-/", $this->getValue());
            if (!is_array($rows)) {
                $rows[] = $row;
            }
            
            foreach ($rows as $row) {
                $value = preg_split("/#/", $row);
                $data[] = array(
                    'id'        => $i++,
                    'name'      => base64_decode($value[0]),
                    'type'      => base64_decode($value[1]),
                    'url'       => base64_decode($value[2]),
                    'identify'  => base64_decode($value[3]),
                    'order'     => base64_decode($value[4]),
                );
            }
        }

        return $data;
    }
}

?>
