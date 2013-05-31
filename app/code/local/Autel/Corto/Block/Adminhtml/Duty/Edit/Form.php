<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * Description of Form
 * 
 * @category    default
 * @package     
 * @author      Marco Mancinelli MPS Sistemi Sas <marco.mancinelli@mps-sistemi.it>
 * @copyright   MPS Sistemi di Mancinelli Marco & C. Sas 
 *
 * create at     30-ago-2012
 */
class Autel_Corto_Block_Adminhtml_Duty_Edit_Form extends Mage_Adminhtml_Block_Widget
{
    protected function _prepareForm()
    {

        $this->setChild('category',
                        $this->getLayout()->createBlock('dccharge/adminhtml_category')
                );
            
//        $form = new Varien_Data_Form();
//
//        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Get HS Code for the specified category')));
//
//                
//        $fieldset->addField('username', 'text', array(
//                'name'  => 'username',
//                'label' => Mage::helper('adminhtml')->__('User Name'),
//                'title' => Mage::helper('adminhtml')->__('User Name'),
//                'required' => true,
//            )
//        );

//
//        $form->setValues($user->getData());
//        $form->setAction($this->getUrl('*/system_account/save'));
//        $form->setMethod('post');
//        $form->setUseContainer(true);
//        $form->setId('edit_form');
//
//        $this->setForm($form);

        return parent::_prepareForm();
    }
}

?>
