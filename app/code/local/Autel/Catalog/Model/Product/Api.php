<?php


/**
 * Classe che estende l'API ufficiale di Magento per la gestione dei prodotti
 * 
 * @category   Autel
 * @package    Autel_Catalog
 * @copyright  Copyright (c) 2009 Autel srl (http://www.autel.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Autel_Catalog_Model_Product_Api extends Mage_Catalog_Model_Product_Api {
	
	/**
	 *Aggiorna i prodotti figli del prodotto configurabile
	*
	 * @param int $parentProductId id del prodotto configurabile gi� presente su database
	 * @param array $configuredProducts Array con gli id dei prodotti da inserire come figli del prodotto configurabile 
	 * @return bool Esito operazione di inserimento figli
	 */
	public function updateConfigurable ($parentProductId, $configuredProducts) 
	{

		$return = true;

		if (is_array($configuredProducts))
		{
			// ricavo i prodotti figli
			$prdModel = Mage::getModel('catalog/product_type_configurable'); // _type_configurable
			$existingChildrenIds  = $prdModel->getChildrenIds($parentProductId);
	
			$configuredProductsId = array_keys($configuredProducts);
			$existingChildrenIdsKeys = array_keys($existingChildrenIds[0]);
	
			$updatedChildrenIds = $configuredProductsId;
		
			if ($existingChildrenIdsKeys!=null)
			{
				foreach ($existingChildrenIdsKeys as $existingChildId)
				{
					$exists = false;
					foreach ($updatedChildrenIds as $updatedChildId)
					{
						if ($updatedChildId == $existingChildId)
							$exists = true;
					}
				
					if (!$exists)
						$updatedChildrenIds[] = $existingChildId;
				}	
			}
			
			// costruisco l'array per passare gli id dei prodotti configurati
			$finalChildren = array();
			foreach ($updatedChildrenIds as $_id)
				$finalChildren[$_id] = array();
/*
		$PWIZ_f = fopen("/home/ale/httpdocs/testws/pwiz.log","w");
 		fwrite($PWIZ_f,"\n---------------------------------------------\n" );
		
		fwrite($PWIZ_f,"\nupdatedChildrenIds---------------------------\n" );
		
		fwrite($PWIZ_f,var_export($finalChildren,true) );
		fclose($PWIZ_f);
		die();
*/
			$prd = Mage::getModel("catalog/product")->load($parentProductId);
			$prd->setConfigurableProductsData($finalChildren);
			
	        if (is_array($errors = $prd->validate()))
			{
	            $this->_fault('data_invalid', implode("\n", $errors));
	        }
	
	
	        try {
	            $prd->save();
	        } catch (Mage_Core_Exception $e) {
	            $this->_fault('data_invalid', $e->getMessage());
	        }
						
			$statusModel = Mage::getModel('catalog/product_status');
			$statusModel->updateProductStatus($parentProductId, 0, 1);
	
			$return = true;
		}
		else 
		{
			$return = false;
		}
	
		return $return;

	}	

	/**
	 * Crea il prodotto configurabile
	*
	 * @param string $set Set di attributi di appartenenza
	 * @param string $sku Sku del prodotto configurabile (contiene parte del COD4 di BestStore)
	 * @param array $configurableAttributes Array con gli attributi da rendere configurabili
	 * @param array $configuredProducts Array con gli id dei prodotti da inserire come figli del prodotto configurabile
	 * @param array $productData Dati base del prodotto configurabile
	 * @return id del prodotto configurabile
	 */
	public function createConfigurable ($set, $sku, $configurableAttributes, $configuredProducts, $productData) {
				
				
		for ($i = 0; $i<count($configurableAttributes); $i++) {
			$configurableAttributes[$i]['attribute_code'] = $configurableAttributes[$i]['code'];
		}
				
		if (!$set || !$sku) {
            $this->_fault('data_invalid');
        }

        $product = Mage::getModel('catalog/product');
        /* @var $product Mage_Catalog_Model_Product */
		
        $product->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId('configurable')
            ->setSku($sku);
						
		$product->getTypeInstance()->getProduct()->setConfigurableAttributesData( $configurableAttributes );
		$product->getTypeInstance()->getProduct()->setConfigurableProductsData( $configuredProducts );

		
        foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($productData[$attribute->getAttributeCode()])) {
                $product->setData(
                    $attribute->getAttributeCode(),
                    $productData[$attribute->getAttributeCode()]
                );
            }
        }

        $this->_prepareDataForSave($product, $productData);

        if (is_array($errors = $product->validate())) {
            $this->_fault('data_invalid', implode("\n", $errors));
        }


        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

		// inizio traduzione attributi

		$storeModel = Mage::getModel('adminhtml/system_store');
		$stores = $storeModel->getStoreCollection();
		$resModel = Mage::getResourceModel('catalog/product_type_configurable_attribute_collection')->setProductFilter($product->getTypeInstance()->getProduct());
		$__configurableAttributes = $resModel->orderByPosition()->load();
		$values = array();
		
		foreach ($__configurableAttributes as $attr) {
			$attribute = Mage::getModel('catalog/entity_attribute');
        	$attribute->setStoreId($this->_getStoreId($store))->load($attr->getProductAttribute()->getId());
			
	        $frontendLabel = $attribute->getFrontend()->getLabel();
	        if (is_array($frontendLabel)) {
	            $frontendLabel = array_shift($frontendLabel);
	        }
			$translations = Mage::getModel('core/translate_string')
           		->load(Mage_Catalog_Model_Entity_Attribute::MODULE_NAME.Mage_Core_Model_Translate::SCOPE_SEPARATOR.$frontendLabel)
           		->getStoreTranslations();
		   
			$attributeData = array(
               'id'            => $attr->getId(),
               'label'         => '',
               'position'      => $attr->getPosition(),
               'values'        => array(),
               'attribute_id'  => $attr->getProductAttribute()->getId(),
               'attribute_code'=> $attr->getProductAttribute()->getAttributeCode(),
               'frontend_label'=> $attr->getProductAttribute()->getFrontend()->getLabel(),
			);	   
			
			foreach ($stores as $tmpStore) {
	            if ($tmpStore->getId() != 0) {
	                $values[$tmpStore->getId()] = isset($translations[$tmpStore->getId()]) ? $translations[$tmpStore->getId()] : $frontendLabel;
					$attributeData['label'] = $values[$tmpStore->getId()];
					$translatedAttribute = Mage::getModel('catalog/product_type_configurable_attribute')
		               ->setData($attributeData)
		               ->setId($attr->getId())
		               ->setStoreId($tmpStore->getId())
		               ->setProductId($product->getId())
		               ->save();	
	            }
	        }
			
		}
		// fine traduzione attributi

        return $product->getId();
//		return 1;
	}

	
}

?>
