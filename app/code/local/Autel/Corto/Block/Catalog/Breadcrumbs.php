<?php

class Autel_Corto_Block_Catalog_Breadcrumbs extends Mage_Catalog_Block_Breadcrumbs {
    
    /**
     * Override della funzione per dare la possibilità di tornare all'Eshop
     *
     * @return Mage_Catalog_Block_Breadcrumbs
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('E-Shop'),
                'title'=>Mage::helper('catalog')->__('Go to Shop Page'),
                'link'=>Mage::getUrl('shop')
            ));

            $title = array();
            $path  = Mage::helper('catalog')->getBreadcrumbPath();

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
            }
        }
        return parent::_prepareLayout();
    }
}

?>
