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
class Autel_Corto_Block_Widget_Headermenu_Menu extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface 
{
    
    const MENU_ESHOP_LINK = 'shop';
    const MENU_HOME_LINK = "home";
    const MENU_CMS_LINK = "cms";
    const MENU_WP_LINK = "wp";
    const MENU_GENERIC_LINK = "generic";
    
    protected $_menu = array();
    
    protected $_isHome = false;
    protected $_isShop = false;
    protected $_isCatalog = false;
    protected $_cmsPageId = '';
    protected $_isSearch = false;
    protected $_isImpulse = false;

    protected function _construct() {
        parent::_construct();
        //$this->setTemplate('veredus/template/widget/product/list.phtml');
        $this->_isHome = Mage::Helper('autelcorto')->getIsHomePage();
        $this->_isShop = Mage::Helper('autelcorto')->getIsShopPage();
        $this->_isCatalog = Mage::helper('autelcorto')->getIsCatalogPage();
        $this->_cmsPageId = Mage::getSingleton('cms/page')->getIdentifier();
        
        
        $controller = Mage::app()->getFrontController()->getRequest()->getControllerName();
        $module = Mage::app()->getFrontController()->getRequest()->getModuleName();
        $action = Mage::app()->getFrontController()->getRequest()->getActionName();
       
        $this->_isSearch = "$module/$controller/$action" == "autelcorto/search/color";
        $this->_isImpulse = "$module/$controller/" == "autelcorto/impulse/";
            
        
    }

    protected function _beforeToHtml() {
        
        $data = Varien_Data_Form_Element_Headermenu::DecodeData($this->getMenu());
        
        foreach ($data as $m) {
            $this->_menu[$m['order']] = $m;
        }
        
        ksort($this->_menu);
                  
        parent::_beforeToHtml();
    }


    protected function _toHtml() {
        
        ob_start();
        include __DIR__ . "/menu.phtml";
        $html = ob_get_clean();
        return $html;
    }
    
    /**
     * Todo identifica se il meù e selezionato o meno
     * @param type $menu
     * @return bool
     */
    protected function isSelectMenu($menu) {
        
        switch ($menu['type']) {
            case self::MENU_HOME_LINK:
                    return $this->_isHome;
                break;
            
            case self::MENU_ESHOP_LINK:
                return ($this->_isShop || $this->_isCatalog);
                break;
            case self::MENU_CMS_LINK:
                $ids = preg_split("/,/", $menu['identify']);
                foreach ($ids as $id) {
                    if ($id != "" && $id == $this->_cmsPageId)
                        return true;
                }
                return false;
                break;
            case self::MENU_WP_LINK: 
                return Mage::registry("_from_wp");
                break;
            default:
                return false;
                break;
        }
        
        
    }
    
    /**
     * Recupera un link dal menu
     * @param type $menu
     */
    protected function getLink ($menu) {
        
        $url = "#";
        switch ($menu['type']) {
            case self::MENU_CMS_LINK:
                // Cms Magento
            case self::MENU_HOME_LINK:
                //Home page Magento
            case self::MENU_ESHOP_LINK:
                //Shop page Magento
                $url = $this->getUrl($menu['url']);
                break;
            case self::MENU_WP_LINK:
                $url = MAge::getStoreConfig('web/unsecure/wp_url') . $menu['url']; 
                break;
            default:
                // Url verso l'esterno
                $url = ($menu['url'] != "") ? $menu['url'] : '#';
                break;
        }
        
        return $url;
    }
    /**
     * Verfico se il menù è da stampare 
     * //non è ne E-shop ne Home    
     * //è Shop e sono in homepage
     * é home e non sono in homepage
     * é shop e non sono in e-shop
     * @param type $menu
     */
    public function isToPrint($menu) {
        
        if ($this->_isSearch || $this->_isImpulse) {
            if ($menu['type'] == self::MENU_HOME_LINK )
                return true;
            
            if ($menu['type'] == self::MENU_ESHOP_LINK)
                return false;
        }
        
        
        if ($menu['type'] == self::MENU_HOME_LINK && $this->_isHome)            
            return false;
        
        if ($menu['type'] == self::MENU_HOME_LINK && $this->_isSearch )
            return true;
        
        if ($menu['type'] == self::MENU_HOME_LINK && (!$this->_isShop && !$this->_isCatalog)) 
            return false;
        
        if ($menu['type'] == self::MENU_ESHOP_LINK && ($this->_isShop || $this->_isCatalog))
            return false;
        
        return true;
    }

    public function getClass($menu) {
	$ids = preg_split("/,/", $menu['identify']);
	if (sizeof($ids) > 0) {
	    return implode(" " , $menu);
	}
	return '';
    }
    
}
?>

