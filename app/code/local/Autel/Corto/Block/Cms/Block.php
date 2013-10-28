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

class Autel_Corto_Block_Cms_Block extends Mage_Cms_Block_Block {

    const ABOUT_MENU_BLOCK = "about-us-menu";
    
    /**
     * Riscritta cosi leggo il blocco una volta sola....
     * ATTENZIONE!!! Non funzina se il blocco viene inserito come widget standard!!!
     * @return type
     */
    protected function _toHtml() {
        
        $blockId = $this->getBlockId();
        $html = '';
        if ($blockId) {
            $block = Mage::getModel('cms/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper Mage_Cms_Helper_Data */
                $helper = Mage::helper('cms');
                $processor = $helper->getBlockTemplateProcessor();
                $html = $processor->filter($block->getContent());
                $this->addModelTags($block);

                if ($blockId == self::ABOUT_MENU_BLOCK) {
                    return $this->_trySetMenu($html);
                }                
                
                $customClass = $this->getCustomClass();
                $customStyle = $this->getCustomStyle();
                $imgBackground = $block->getBackgroundImageUrl();
                        
                if ($customClass || $customStyle || $imgBackground) {

                    $html = "<div class=\"mps-block-background $customClass\" style=\"background: transparent url('$imgBackground') 50% 50% no-repeat; $customStyle\">"
                            .$html . "</div>";                        
                }
                
            }            
        }        
        return $html;
    }
    
    private function _trySetMenu($html) {
        $pageId = Mage::getSingleton('cms/page')->getIdentifier();
        
        //$reg = "/<li id=\"$pageId\">/';
        
        $html = preg_replace("/<li id=\"$pageId\">/", "<li id=\"$pageId\" class=\"selected\">", $html);
        
        return $html;
    }
}

?>
