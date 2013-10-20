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
class Autel_Corto_Block_Widget_Cms_Block_Magic extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface 
{
    private $_cacheID = "AUTEL_CORTO";    
    
    protected function _construct() {
        $this->_cacheId .= "_" . Mage::app()->getStore()->getId();
        parent::_construct();
        
    }

    protected function _beforeToHtml() {                         
 
        $htmlBlocks = $this->_loadFromCache();
        if (!is_array($htmlBlocks)) {
            $htmlBlocks = $this->getBlockLists();
            $this->_saveInCache($htmlBlocks);
        }

        $this->setHtmlBlockLists($htmlBlocks);
        
        parent::_beforeToHtml();
    }


    protected function _toHtml() {
        
        $html = "";
        foreach ($this->getHtmlBlockLists() as $block) {
            $html .= $block;
            $html .= '<div class="clearer"></div>';
        }
        
        return $html;
    }
    
    protected function getBlockLists() {
        
        $pageId = Mage::getBlockSingleton('cms/page')->getPage()->getPageId();
        $pageIdentifier = Mage::getBlockSingleton('cms/page')->getPage()->getIdentifier();
        $blockList = Mage::getModel('autelcorto/cms_pageblocks')->getCollection()
                        ->addPageFilter($pageId)->sort();
        
        $blocksHTML = array();
        
        $isFirst = true;

        foreach ($blockList as $block) {

            $htmlBlock = $this->getLayout()->createBlock('cms/block')->setBlockId($block->getBlockId());
            $style = "";
            $styleWidth = "";
            $styleHeight = "";
            $class = "";
            if ($block->getFill()) {
                if ($block->getFill() == 2) {
                    $class .= "mps-force-fill";
                } else {
                    $styleHeight .= "width: 100%!important;";
                }
                $styleWidth  .= "width: 100%!important;";                
            } 
            if ($block->getWidth() > 0) {
              $styleWidth .= "width: " . $block->getWidth() . "px!important;";
            }
            if ($block->getHeight() > 0) {
              $styleHeight .= "height: " . $block->getHeight() . "px!important;";
            }
            
            
            if ($block->getStyle()) {
                $style .= $block->getStyle();
            }
            
            if ($block->getClass()) {
                $class .= " " . $block->getClass();
            }
            
            $htmlBlock->setCustomClass($class);
            $htmlBlock->setCustomStyle("$styleHeight $styleWidth $style");

            $html = $htmlBlock->toHtml();
            if ($isFirst && $pageIdentifier == Mage::app()->getStore()->getConfig('web/default/cms_home_page')){
                $html = '<div class="clearer" style="width: 100%; height: 97px"></div>' . $html;
                $isFirst = false;
            }
            
            $blocksHTML[] = $html;
        }
        
        return $blocksHTML;
    }
    
    private function _loadFromCache() {
        $cache = Mage::getModel("core/cache");
        return unserialize($cache->Load($this->_cacheID));
    }
    
    private function _saveInCache($htmlArray) {
        $cache = Mage::getModel("core/cache");
        $cache->save(serialize($htmlArray),$this->_cacheID, array($this->_cacheID), null);
    }
}

?>
