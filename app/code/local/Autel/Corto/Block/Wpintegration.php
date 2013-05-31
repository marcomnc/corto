<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mediarequest
 *
 * @author marcoma
 */
class Autel_Corto_Block_Wpintegration extends Mage_Core_Block_Template
{
    public function _construct() {        
        parent::_construct();
    }
    
    public function toHtmlPopUp() {        
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
        $html.= '<div class="page-popup">';
        $html.= '<img id="vertex-ne" src="'. Mage::getDesign()->getSkinUrl('images/fancy-vertici.png') . '"/>';
        $html.= '<img id="vertex-nw" src="'. Mage::getDesign()->getSkinUrl('images/fancy-vertici.png') . '"/>';
        $html.= '<div id="title">&nbsp;</div>';
        $html.= '<div class="pop-up-content">';
        $html.= $this->toHtml();
        $html.= '</div>';    
        $html.= '<img id="vertex-se" src="' . Mage::getDesign()->getSkinUrl('images/fancy-vertici.png') . '"/>';
        $html.= '<img id="vertex-sw" src="' . Mage::getDesign()->getSkinUrl('images/fancy-vertici.png') . '"/>';
        $html.= '</div>';
        return $html;
    }
}

?>
