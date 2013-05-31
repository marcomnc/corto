<?php

/**
 * Classe che estende l'API ufficiale di Magento per la gestione degli attributi
 * 
 * @category   Autel
 * @package    Autel_Catalog
 * @copyright  Copyright (c) 2009 Autel srl (http://www.autel.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * MOD2011
 */
class Autel_Catalog_Model_Product_Attribute_Api extends Mage_Catalog_Model_Product_Attribute_Api
{
    /**
     * Recuper l'id dell'attributo
     * 
     * @author Marco Mancinelli      
     * @param string $attributeCode Codice dell'attributo
     * @return int $attributeId Id dell'attributo (Se 0 l'attributo non esiste) 
     */
    public function getattributeId($attributeCode)  
    {
//        Mage::Log("Attributo ricercato $attributeCode");
//        Mage::Log("Tipo Entity " . Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
        
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
                                ->getIdByCode( 'catalog_product', $attributeCode);
//        Mage::Log($attributeId);
        return is_null($attributeId)?0:$attributeId;
        
    }   

    /**
     * Aggiunge un valore all'attributo con scelta multipla.
     * Codice di esempio preso da: http://www.magentocommerce.com/boards/viewthread/26234/
     * @return l'id del valore appena inserito
     * @param object $attributeId Id dell'attributo
     * @param object $optionValue Il valore da aggiungere
     */
    public function addOption($attributeId, $optionValue)
    {

        $attribute_model = Mage::getModel('eav/entity_attribute');
        $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');

        $attribute = $attribute_model->load($attributeId);

        $attribute_table = $attribute_options_model->setAttribute($attribute);
        $options = $attribute_options_model->getAllOptions(false,true);

        $exists = false;

        foreach ($options as $option)
        {
            if ($option['label'] == $optionValue)
            {
                $exists = true;
                break;
            }
        }

        if (!$exists)
        {
            $value['option'] = array($optionValue);
            $result = array('value' => $value);
            $attribute->setData('option', $result);
            $attribute->save();
        }
        else
        {
            $this->_fault('duplicated_value');
        }

        // ricavo nuovamente la lista dei valori per ritornare l'id
        $options = $this->optionDefaults($attributeId);
//		$PWIZ_f = fopen("/var/www/magento1211_empty/var/pwiz.log","a");
// 		fwrite($PWIZ_f,"\n---------------------------------------------\n" );
//		fwrite($PWIZ_f,"hai chiamato Autel_Catalog_Model_Product_Attribute_Api\n" );
//		fwrite($PWIZ_f,var_export($options,true) );
//		fclose($PWIZ_f);
        foreach ($options as $option)
        {
            if ($option['label'] == $optionValue)
            {
                return $option['value'];
            }
        }

        return 0;
    }

    // attributeId = id attributo di magento (numerico)
    // optionValue = valore di default (admin) (codice modello+colore)
    // valueTranslation = valore tradotto
    // storeId = store view id
    public function addOptionValue($attributeId, $optionValue, $valueTranslation, $storeId)
    {
        $model = Mage::getModel('catalog/entity_attribute');
        $model->load($attributeId);
//		$options = $this->optionDefaults($attributeId);

        $options = array();
        $options = $this->optionDefaults($attributeId);

//		$PWIZ_f = fopen("/home/ale/httpdocs/testws/pwiz.log","w");
// 		fwrite($PWIZ_f,"\n---------------------------------------------\n" );
//		fwrite($PWIZ_f,"\nATTRIBUTE ID  $attributeId---------------------------\n" );

        $_data = array();

//		fwrite($PWIZ_f,"\nOPTIONS: \n" );
//		fwrite($PWIZ_f,var_export($options,true) );

        $valueId = 0;
        foreach ($options as $option)
        {
            if ($option['label'] == $optionValue)
            {
                $valueId = $option['value'];
                $_data['option']['value'][$valueId] = array();
                $_data['option']['value'][$valueId][0] = $option['label'];

                break;
            }
        }

//		fwrite($PWIZ_f,"\nVALUE ID  $valueId---------------------------\n" );

        $_data['option']['value'][$valueId][$storeId] = $valueTranslation;

        foreach (Mage::app()->getStores() as $_store)
        {
            if ($_store->getId() != $storeId)
            {
                $_options = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                ->setAttributeFilter($attributeId)
                                ->setStoreFilter($_store->getId(), false)
                                ->load()
                                ->toOptionArray();

//				fwrite($PWIZ_f,"\n_OPTIONS: \n" );
//				fwrite($PWIZ_f,var_export($_options,true) );					
//				fwrite($PWIZ_f,"\n\nNegozio " . $_store->getId() . "\n" );
                foreach ($_options as $_option)
                {
                    if ($_option['value'] == $valueId)
                    {
                        $_data['option']['value'][$valueId][$_store->getId()] = $_option['label'];
                        break;
                    }
                }
            }
        }

//MM - non salvava cambiato        
        $attr =  Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'color');        
        $attr->addData($_data);
        $attr->save();

        return 0;
//		fwrite($PWIZ_f,var_export($_data,true) );
//		fclose($PWIZ_f);
    }

    /**
     * Ricava i default delle opzioni degli attributi (ovvero i valori dello store 0, non di quello di default).
     *
     * @param int $attributeId
     * @return array
     */
    public function optionDefaults($attributeId)
    {
        $attribute = Mage::getModel('catalog/entity_attribute');
        $attribute
                ->setStoreId(0)
                ->load($attributeId);

        /* @var $attribute Mage_Catalog_Model_Entity_Attribute */
        if (!$attribute->getId())
        {
            $this->_fault('not_exists');
        }
        $options = array();
        if ($attribute->usesSource())
        {
            $options = Mage::getResourceModel('eav/entity_attribute_option_collection')
                            ->setAttributeFilter($attribute->getId())
                            ->setStoreFilter(0, false)
                            ->load()
                            ->toOptionArray();
        }
        return $options;
    }
    

    /* SIZE_TRANSLATION MOD2011 */

    private $_conn = null;

    private function _dbConnect()
    {
        if ($this->_conn == null)
        {
            $localConfigFile = Mage::getRoot() . DS . "etc" . DS . 'local.xml';
            $localConfig = simplexml_load_file($localConfigFile);
            $host = $localConfig->global->resources->default_setup->connection->host;
            $dbname = $localConfig->global->resources->default_setup->connection->dbname;
            $username = $localConfig->global->resources->default_setup->connection->username;
            $password = $localConfig->global->resources->default_setup->connection->password;
            //$socket = $localConfig->global->resources->default_setup->connection->socket;
            $this->_conn = new mysqli($host, $username, $password, $dbname/*,'3306',$socket*/) or die("Errore connessione db: " . $this->_fault('db_error', mysqli_connect_error()));
        }
        return $this->_conn;
    }

    public function loadSizeTranslations($data)
    {
        if (!isset($data['file']) || !isset($data['file']['mime']) || !isset($data['file']['content']))
        {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('File not specified.'));
        }

        $fileContent = @base64_decode($data['file']['content'], true);
        if (!$fileContent)
        {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('File content is not valid base64 data.'));
        }
        unset($data['file']['content']);

        // analizzo il contenuto del file

        $query = '';

        

        /*
        $localConfigFile = Mage::getRoot() . DS . "etc" . DS . 'local.xml';
        $localConfig = simplexml_load_file($localConfigFile);
        $host = $localConfig->global->resources->default_setup->connection->host;
        $dbname = $localConfig->global->resources->default_setup->connection->dbname;
        $username = $localConfig->global->resources->default_setup->connection->username;
        $password = $localConfig->global->resources->default_setup->connection->password;

        $query .= "$host - $dbname - $username - $password<br />";

        $server = new mysqli($host, $username, $password, $dbname,'3306','/tmp/mysql.sock') or die("Errore connessione db: " . $this->_fault('db_error', mysqli_connect_error()));
        $query .= $server->connect_error;
*/
        $rows = explode("\n", $fileContent);
        if (count($rows)>1)
        {
            $this->_dbConnect();
            $delete = "DELETE FROM size_translation";
            if (!$this->_conn->query($delete))
            {
                $query .= $this->_conn->error . ' DEL<br />';
            }

            $bs_values = array();

            foreach ($rows as $row)
            {
                $fields = explode(";", str_replace("\"", "", $row));
                if ($fields[1]=='BS')
                {
                    // è la prima riga e ricavo le descrizioni di beststore
                    $bs_values = array();
                    for ($i=3;$i<18;$i++)
                        $bs_values[] = $fields[$i];
                }
                else
                {
                    for ($i=3;$i<18;$i++)
                    {
                        if (trim($fields[$i])!='')
                        {
                            $insert = "INSERT INTO size_translation (`size_group`,`target`,`bs_value`,`lang_code`,`lang_value`) VALUES ('$fields[0]','$fields[2]','".$bs_values[$i-3]."','$fields[1]','$fields[$i]')";
                            $query .= $insert.'<br />';
                            if (!$this->_conn->query($insert))
                            {
                                $query.= $this->_conn->error . 'INS<br />';
                            }
                        }
//                        
                    }
                }
            }
        }

        unset($fileContent);

        return $query;
    }

}

?>
