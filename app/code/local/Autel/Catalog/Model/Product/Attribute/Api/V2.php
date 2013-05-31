<?php
/**
 * Catalog product attribute set api V2
 *
 * @category   Autel
 * @package    Autel_Catalog
 * @author     Marco Mancinelli
 */

class Autel_Catalog_Model_Product_Attribute_Api_V2 extends Autel_Catalog_Model_Product_Attribute_Api 
{
    protected $_save = 0 ;
    protected $_array = array();
    protected $_hlp;
    
    public function __construct() {
        $this->_hlp = Mage::helper("autelcatalog");
    }
    
    /**
     * Recupera gli l'elenco degli attributi. 
     * A differenza della funzione standard recuper tutti i dati degli attributi
     * per elaborazioni più complete.     
     * 
     * @param array $filter array contente i campi su cui efetturare il filtro
     *              nella struttura [attributo da filtrare]=>[valore del filtro]
     * @return JSON Lista di oggetti 
     */
    public function getItems ($setid, $filter = array()) {
        $attributes = Mage::getModel('catalog/product')->getResource()
                ->loadAllAttributes()
                ->getSortedAttributes($setid);
        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ((!$attribute->getId() || $attribute->isInSet($setid))
                    && $this->_isAllowedAttribute($attribute)) {

                if (!$attribute->getId() || $attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $attributeSet = array();
                $attributeSet["AttributeId"] = ($attribute->getId()."" != "")?$attribute->getId():0; 
                $attributeSet["Code"] = $attribute->getAttributeCode();
                $attributeSet["Type"] = $attribute->getFrontendInput();
                $attributeSet["Required"] = $attribute->getIsRequired();
                $attributeSet["Scope"] = $scope;
                $attributeSet["Label"] = $attribute->getFrontendLabel();
                $attributeSet["BaseType"] = ucwords($attribute->getBackendType());
                $attributeSet["IsCustom"] = ($attribute->getIsUserDefined().""!="")?$attribute->getIsUserDefined():0;
                
//                $attributeSet = new stdClass();
//                $attributeSet->AttributeId = $attribute->getId(); 
//                $attributeSet->Code = $attribute->getAttributeCode();
//                $attributeSet->Type = $attribute->getFrontendInput();
//                $attributeSet->Required = $attribute->getIsRequired();
//                $attributeSet->Scope = $scope;
//                $attributeSet->Label = $attribute->getFrontendLabel();
//                $attributeSet->BaseType = ucwords($attribute->getBackendType());
//                $attributeSet->IsCustom = $attribute->getIsUserDefined();

                $result[] = $attributeSet;                
            }
        }
        return json_encode($result);
    }

    public function getOptionJSON ($attributeName, $storeId) {
        $this->_hlp->debug("Leggo Valori per Attributo $attributeCode");
        $retVal = array();
        $attrList = Mage::helper("autelcatalog/product")->getOptionValue($attributeName, $storeId);
        $this->_hlp->debug("Recuperati "  . sizeof($attrList) . " valori");
        foreach ($attrList  as $attr) {
            $obj = new stdClass();
            $obj->Key = $attr['code'];
            $obj->Value = $attr['description'];
            $retVal[] = $obj;
        }

        return json_encode($retVal);
    }
    
    public function addOptionJSON ($attributeName, $storeId, $optionJSON)
    {
        
        $value = json_decode($optionJSON);
$this->_hlp->debug('Inizio impostazine di ' . sizeof($value));                
        //Recupero l'id dell'attributo
        $attrId = $this->getattributeId($attributeName);
//$this->_hlp->debug($attrId) ;       
        if ($attrId > 0) {
            foreach ($value as $obj) {
                //do per scontato che la key è il valore di default
//$this->_hlp->debug($obj);                
//$this->_hlp->debug($obj->Key);
//$this->_hlp->debug($obj->Value);                
                try
                {
                    $this->addOption($attrId, $obj->Key);
                } catch (Exception $e) {
                    if ($e->getMessage()!='duplicated_value') {
$this->_hlp->debug('Importazione completata con errori. Errore in fase di inserimento codice');                                
                        return -2;
                    }
                }
                if ($storeId != 0) {
                    $this->addOptionValue($attrId, $obj->Key, $obj->Value, $storeId);
                }
            } 
        } 
        else {
$this->_hlp->debug("Importazione completata con errori. Non esiste l'attributo");            
            return -1;        
        }
$this->_hlp->debug('Importazione completata senza errori');        
        return 0;
    }
    
    
    private function _getTableName($tname, $alias = null) 
    {
        if (!is_null($alias)) {
            return array($alias, Mage::getSingleton('core/resource')
                                        ->getTableName($tname));
        }else{
            return Mage::getSingleton('core/resource')
                                        ->getTableName($tname);
        }
    }
    
    private function _existsOption($attributeId, $storeId, $value) 
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $db->select()
                     ->from($this->_getTableName('eav_attribute_option', 'opt'))
                     ->join($this->_getTableName('eav_attribute', 'attr'),
                            'attr.attribute_id = opt.attribute_id')
                     ->join($this->_getTableName('eav_attribute_option_value ', 'val'),
                            "ON val.option_id = opt.option_id AND val.store_id = $storeId")
                     ->where("attr.attribute_id = ?", $attributeId)
                     ->where("val.value = ?", $value)
                     ->reset(Zend_Db_Select::COLUMNS)
                     ->coulmns('opt.option_id as option_id');
        $this->_hlp->debug($select->__toString());
        $row = $select->fetchOne();
        return (sizeof($row) == 1)?$row['option_id']:false;
        
    }
}
?>
