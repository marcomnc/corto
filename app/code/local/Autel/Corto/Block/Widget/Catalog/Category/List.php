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
class Autel_Corto_Block_Widget_Catalog_Category_List extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface 
{
    protected function _construct() {
        parent::_construct();
        
    }

    protected function _beforeToHtml() {                         
        
        $category = array();
        
        $categoryIds = preg_split("/\//", $this->getIdPath());
        if (isset($categoryIds[1])) {
            //$cat = preg_split("/,/", Mage::getModel('catalog/category')->Load($categoryIds[1])->getChildrenCategories());
            $cat = Mage::getModel('catalog/category')->Load($categoryIds[1])->getChildrenCategories();
            foreach ($cat as $children) {
                $category[] = $children;
            }
        }

        $this->setChilds($category);
        
        parent::_beforeToHtml();
    }


    protected function _toHtml() {
        
        ob_start();
        include __DIR__ .'/list.phtml';
        $html = ob_get_clean();
        return $html;
    }
    
    
}
?>

