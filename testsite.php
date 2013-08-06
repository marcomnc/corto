<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('app/Mage.php'); //Path to Magento
umask(0);

$siteDown = "";

foreach (Mage::app()->getStores() as $_store) {    

if ($_store->getIsActive()) {    

    Mage::app()->setCurrentStore($_store->getId());

//echo "test " . Mage::getBaseUrl() . "<br>";
    if (strrpos (file_get_contents( Mage::getBaseUrl()), '<meta name="Develop" content="by Autel S.r.L." />') === false)
    {
        $siteDown .= "\n" .  Mage::getBaseUrl() ;
    }   
    
//echo "test " . Mage::getBaseUrl() . "shop/<br>";
    if (strrpos (file_get_contents( Mage::getBaseUrl() . 'shop/'), '<meta name="Develop" content="by Autel S.r.L." />') === false)
    {
        $siteDown .= "\n" .  Mage::getBaseUrl() . 'shop/' ;
    }
}
}

if ($siteDown != "") {
    // il sito non è attivo!!!!
    $to = "marco@mancinellimarco.it";
    //$to .= ",mnc74@hotmail.it";    
    $to .= ",valerio.massari@corto.com";
    $to .= ",lady@autel.it";
    $object = "CORTO MOLTEDO: Web Site Down!";
    $text = "I SEGUENTI SITI/INDIRIZZI $siteDown \n IN QUESTO MOMENTO RISULTANO DOWN!!";
    $header = "From: no-reply@corto.com";
    $headers = "MIME-Version: 1.0\n" ;
    $headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
    $headers .= "X-Priority: 1 (Highest)\n";
    $headers .= "X-MSMail-Priority: High\n";
    $headers .= "Importance: High\n";
    mail($to, $object, $text, $header);        
}



?>

