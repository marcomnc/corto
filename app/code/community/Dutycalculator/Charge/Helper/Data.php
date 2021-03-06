<?php
class Dutycalculator_Charge_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $logFile = 'dc_api_calls.log';
	const DC_EXPORT_ALL_PRODUCTS = 1;
	const DC_EXPORT_ONLY_PRODUCTS_WITHOUT_DC_ID = 2;

	const DC_DELIVERY_TYPE_DDP = 'ddp';
	const DC_DELIVERY_TYPE_DDU = 'ddu';

	public function getDeliveryDutyPaidType()
	{
		return self::DC_DELIVERY_TYPE_DDP;
	}

	public function getDeliveryDutyUnpaidType()
	{
		return self::DC_DELIVERY_TYPE_DDU;
	}

	public static function getExportOptions()
	{
		return array(
			array('value' => self::DC_EXPORT_ALL_PRODUCTS,
				  'label' => Mage::helper('dccharge')->__('Include all products')),
			array('value' => self::DC_EXPORT_ONLY_PRODUCTS_WITHOUT_DC_ID,
				  'label' => Mage::helper('dccharge')->__('Include only products that have no DutyCalculator ID')));
	}

	public static function getWorkingDir()
	{
		Mage::getConfig()->getOptions()->createDirIfNotExists(Mage::getBaseDir('var') . DS . 'dutycalculator');
		return Mage::getBaseDir('var') . DS . 'dutycalculator' . DS;
	}

    public function formatFee($amount){
        return Mage::helper('charge')->__('Import duty and taxes');
    }

	public function getDeliveryTypes()
	{
		return array(self::DC_DELIVERY_TYPE_DDP => $this->__('Duty Delivery Paid'), self::DC_DELIVERY_TYPE_DDU => $this->__('Duty Delivery Unpaid'));
	}

	public function sendRequest($action, $params = array())
	{
		$uri = Mage::getStoreConfig('dc_charge_extension/dccharge/api_uri') . Mage::getStoreConfig('dc_charge_extension/dccharge/api_key') . '/' . $action . '/';
		if ($params)
		{
			$uri .= '?';
			foreach ($params as $key => $param)
			{
				if (is_array($param))
				{
					foreach ($param as $idx => $value)
					{
						$uri .= $key . '[' . $idx . ']=' . urlencode($value). '&';
					}
				}
				else
				{
					$uri .= $key . '=' . urlencode($param). '&';
				}
			}
		}
		$uri = rtrim($uri, '&');
		/* @var $cache Mage_Core_Model_Cache */
		$cache = Mage::getSingleton('core/cache');
		$uriCacheKey = sha1($uri);
		$responseBody = $cache->load($uriCacheKey);
		try
		{
			new SimpleXMLElement($responseBody);
		}
		catch (Exception $ex)
		{
			if ($responseBody)
			{
				$this->logApiErrors($responseBody);
			}
			$curlHandler = curl_init();
			curl_setopt($curlHandler, CURLOPT_URL, $uri);
			curl_setopt($curlHandler, CURLOPT_POST, 0);
			ob_start();
			$result = curl_exec($curlHandler);
			$responseBody = ob_get_contents();
			ob_end_clean();
			if (!$result)
			{
				$error = curl_error($curlHandler) . '(' . curl_errno($curlHandler) . ')';
			}
			else
			{}
			curl_close($curlHandler);
			$cache->save($responseBody, $uriCacheKey, array('dutycalculator'), (60 * 60 * 24));
		}
		return $responseBody;
	}

	public function canUseForCountry($countryCode)
	{
		if (Mage::getStoreConfig('dc_charge_extension/dccharge/allow-specific-countries') == 1)
		{
			$availableCountries = explode(',', Mage::getStoreConfig('dc_charge_extension/dccharge/specific-countries'));
			if (!in_array($countryCode, $availableCountries))
			{
				return false;
			}
		}
		return true;
	}

	public function canUseForShippingMethod($shippingMethod)
	{
		if (Mage::getStoreConfig('dc_charge_extension/dccharge/allow-specific-shipping-methods') == 1)
		{
			$availableShippingMethods = explode(',', Mage::getStoreConfig('dc_charge_extension/dccharge/specific-shipping-methods'));
			$shippingMethod = explode('_', $shippingMethod);
			$shippingMethodCode = $shippingMethod[0];
			if (!in_array($shippingMethodCode, $availableShippingMethods))
			{
				return false;
			}
		}
		return true;
	}

	public function logApiErrors($message)
	{
		try {
			$logDir  = Mage::getBaseDir('var') . DS . 'log';
			$logFile = $logDir . DS . $this->logFile;

			if (!is_dir($logDir)) {
				mkdir($logDir);
				chmod($logDir, 0777);
			}

			if (!file_exists($logFile)) {
				file_put_contents($logFile, '');
				chmod($logFile, 0777);
			}

			$format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
			$formatter = new Zend_Log_Formatter_Simple($format);
			$writer = new Zend_Log_Writer_Stream($logFile);
			$writer->setFormatter($formatter);
			$logger = new Zend_Log($writer);
			if (is_array($message) || is_object($message)) {
				$message = print_r($message, true);
			}
			$logger->log($message, Zend_Log::ERR);
		}
		catch (Exception $e)
		{}
	}
}