<?php

class Autel_Corto_Block_Page_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs {
    
    /**
     * Override della funzione per dare la possibilità di tornare all'Eshop
     *
     * @return Mage_Catalog_Block_Breadcrumbs
     */
    
    function addCrumb($crumbName, $crumbInfo, $after = false)
    {
        if ($crumbName == "home" && $this->getRequest()->getModuleName() != 'cms') {
            $crumbInfo = array(
                'label'=>Mage::helper('catalog')->__('E-Shop'),
                'title'=>Mage::helper('catalog')->__('Go to Shop Page'),
                'link'=>Mage::getUrl('shop'));
        }
        
        
        $this->_prepareArray($crumbInfo, array('label', 'title', 'link', 'first', 'last', 'readonly'));
        if ((!isset($this->_crumbs[$crumbName])) || (!$this->_crumbs[$crumbName]['readonly'])) {
           $this->_crumbs[$crumbName] = $crumbInfo;
        }
        return $this;
    }
    
}

?>
