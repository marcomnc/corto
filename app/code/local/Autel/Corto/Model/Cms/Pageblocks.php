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

class Autel_Corto_Model_Cms_Pageblocks extends Mage_Core_Model_Abstract {
    
    protected function _construct()
    {        
        $this->_init('autelcorto/cms_pageblocks');
    }
    
    public function getId() {
        return $this->getEntityId();
    }
    
    public function deleteForPage($pageId) {
        if ($pageId > 0) {
            $pageBlocks = $this->getCollection()->addPageFilter($pageId);
            foreach ($pageBlocks as $pageBlock) {
                $pageBlock->delete();
            }
        }
    }
    
    public function getSelectBlockForPageArray($pageId) {
        $return = array();
        foreach ($this->getSelectBlockForPage($pageId) as $id) {
            $return[] = $id->getBlockId();
        }
        return $return;
    }
    
    public function getSelectBlockForPage($pageId) {
        $collection = $this->getCollection();
        $collection->getSelect()
                    ->Where('page_id = ?', $pageId)
                    ->Reset(Varien_Db_Select::COLUMNS)
                    ->columns('block_id');

        return $collection;
    }
    
}

?>
