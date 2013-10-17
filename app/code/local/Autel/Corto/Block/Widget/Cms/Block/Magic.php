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
        
        $blockList = Mage::getModel('autelcorto/cms_pageblocks')->getCollection()
                        ->addPageFilter($pageId)->sort();
        
        $blocksHTML = array();

        foreach ($blockList as $block) {

            $htmlBlock = $this->getLayout()->createBlock('cms/block')->setBlockId($block->getBlockId());
            $style = "";
            $class = "";
            if ($block->getFill()) {
                $class .= "mps-force-fill";
                $style .= "width: 100%!important;";
            } else {
                if ($block->getWidth() > 0) {
                  $style .= "width: " . $block->getWidth() . "px!important;";
                }
                if ($block->getHeight() > 0) {
                  $style .= "height: " . $block->getHeight() . "px!important;";
                }
            }
            
            if ($block->getStyle()) {
                $style .= $block->getStyle();
            }
            
            if ($block->getClass()) {
                $class .= " " . $block->getClass();
            }
            
            $htmlBlock->setCustomClass($class);
            $htmlBlock->setCustomStyle($style);

            $blocksHTML[] = $htmlBlock->toHtml();
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
