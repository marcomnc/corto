<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *  
 *
 * @category    
 * @package        
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */

class Autel_Corto_Block_Adminhtml_Cms_Block_Edit_Form extends Mage_Adminhtml_Block_Cms_Block_Edit_Form {
    
    protected function _prepareForm() {
        parent::_prepareForm();
        
        $form = $this->getForm();
        $fieldset = $form->addFieldset('autel_corto',
            array('legend'=>Mage::helper('autelcorto')->__('Impostazione agguntive corto'))
        );
        $fieldset->addField('background_image', 'image', 
                array(
                    'label'     => Mage::helper('autelcorto')->__('Image Background'),
                    'required'  => false,
                    'name'      => 'background_image',
                )
            );
                

        $model = Mage::registry('cms_block');
        
        $form->addValues(array('background_image' => $model->getBackgroundImageUrl()));
        $form->setData('enctype', 'multipart/form-data');
        $this->setForm($form);
        
        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
    }
    
}

?>
