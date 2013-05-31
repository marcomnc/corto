<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Test WS Bs</title>
        <style>
            body {font-family: "Verdana", "Arial"}
            form {width: auto}
            form div {width: 100% }
            form div span {width: 20%; border-bottom: 1px solid}
            form div input { }
            table {border: 1px solid}
            .title {color: white; background-color: black}
            td {border: 1px solid gray}
        </style>        
    </head>
    <body>
        <form method="POST" action="MoltedoServiceTest.php">
            <div>
                <span>Codice Articolo:</span>
                <input type="text" name="sku"/>
                <span>Taglia</span>
                <select name="size">
                    <option checked value="U">Unica</option>
                </select>
            </div>            
            <div>
                <span>Tipo di Dettaglio da tornare</span>
                <select name="type">
                    <option value="0">Tutti i magazzini</option>
                    <option value="1">Singolo magazzino</option>
                </select>
            </div> 
            <input type="submit" value="Inquiry"/>
        </form>        
        <?php
/**
 * Classe per passare i parametri
 */        
class GetProductsQuantity {
    /**
     * Lista codici articolo separata da pipe
     * il codice deve essere seguito da ; + taglia 
     * @var String
     */
    public $ProductsSizeList = "";
    /**
     * Tipo di elenco da ritornare effettuare:
     * 0 - Tutti i negozi
     * 1 - Dettaglio per Singolo negozio
     * @var int 
     */
    public $QuantityType = 1;
    
    public function setQuantityType($q) {
        if ($q != 0 && $q != 1) {
            $q = 0;
        }
        $this->QuantityType = $q;
    }
    
    public function setProductsSizeList($sku, $size, $add=false) {
        if ($size == "") {
            $size = "U";
        }
        $sku = "$sku;$size";
        if (!$add ||$this->ProductsSizeList=="") {
            $this->ProductsSizeList = $sku;
        } else {
            $this->ProductsSizeList .="|$sku";
        }
    }
}

if ($_POST["sku"] != "") {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);
    try {
        $ms = new SoapClient('http://192.168.0.201/MagentoServices/MoltedoService.svc?wsdl');
        $gpc = new GetProductsQuantity();
        $gpc->setQuantityType($_POST["type"]);
        if (strpos($_POST["sku"], "|") !== false) {
            $gpc->ProductsSizeList = $_POST["sku"];
        } else {
            $gpc->setProductsSizeList($_POST["sku"], $_POST["size"]);
        }
        
        $result = $ms->GetProductsQuantity($gpc);            
        if (sizeof($result->GetProductsQuantityResult->ProductQuantity)>0): ?>
        <hr>
        <table>
            <tr class="title">
                <td>SKU</td><td>Size</td><td>Qtà</td><td>Priorità</td><td>Mail</td><td>Descrizione</td><td>deposito</td>
            </tr>                            
            <?php foreach ($result->GetProductsQuantityResult->ProductQuantity as $r):?>
            <tr>
                <td><?php echo $r->sku;?></td>
                <td><?php echo $r->size;?></td>
                <td><?php echo $r->quantity;?></td>
                <td><?php echo $r->priority;?></td>
                <td><?php echo $r->depp_mail;?></td>
                <td><?php echo $r->depp_desc;?></td>
                 <td><?php echo $r->depp;?></td>
            </tr>                
            <?php endforeach;?>
        </table>
            
<?php            
        endif; 
        
        
    } catch (Exception $e) {
        echo '<div style="border:3px solid red;width: 100%">';
        print_r ($e);
        echo "</div>";
    }
}
        
        ?>
        </pre>
    </body>
</html>
