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
class Varien_Data_Form_Element_Custommultiselect extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $this->addClass('select');        
        $html = '<ul id="'.$this->getHtmlId().'" '.$this->serialize($this->getHtmlAttributes()).'>'."\n";
        
        $value = $this->getValue();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        
        if ($values = $this->getValues()) {
            foreach ($values as $key => $option) {
                if (!is_array($option)) {
                    $html.= $this->_optionToHtml(array(
                        'value' => $key,
                        'label' => $option),
                        $value
                    );
                }
                elseif (is_array($option['value'])) {
                    foreach ($option['value'] as $groupItem) {
                        $html.= $this->_optionToHtml($groupItem, $value);
                    }
                    
                }
                else {
                    $html.= $this->_optionToHtml($option, $value);
                }
            }
        }        
        $html .= '</ul>';

        return $html;
    }
    
    protected function _optionToHtml($item, $selected) {
        
        $htmlOpt  = '<li' . ((isset($item['style'])) ? 'style="'.$item['style'].'"' : '') . '>';
        $htmlOpt .= '<label for="'. $this->getJsObjectName() . '_' . $item['value'] .'">';
        $htmlOpt .= '<input type="checkbox" name="' . $this->getName() . '[]"';
        $htmlOpt .= '  id="'. $this->getJsObjectName() . '_' . $item['value'] .'" value="' . $item['value'] .'" class="checkbox"';
        if (in_array((string)$item['value'], $selected)) {
            $htmlOpt .= ' checked';
        }
        $htmlOpt .= '/>';
        $htmlOpt .= $item['label'];
        $htmlOpt .= '</label>';
        $htmlOpt .= '</li>';

        return $htmlOpt;
    }
    
    public function getJsObjectName() {
         return $this->getHtmlId() . 'ElementControl';
    }
    
    public function getHtmlAttributes()
    {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'size', 'tabindex');
    }
}

?>
