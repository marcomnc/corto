<?php
/**
 * @package:    Core
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2012 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    1.0.0
 */

abstract class EcommerceTeam_Core_Model_Observer_Abstract
{
    /**
     * @abstract
     * @return string
     */
    abstract protected function _getExtensionName();

    protected $_urlPrefix;
    protected $_installUrl = 'edownloadable/install';
    protected $_infoUrl    = 'edownloadable/install/info';

    public function __construct()
    {
        $this->_urlPrefix = strval(Mage::getConfig()->getNode('ecommerceteam/url'));
    }

    /**
     * @return string
     */
    protected function _getInstallUrl()
    {
        return $this->_urlPrefix . $this->_installUrl;
    }

    /**
     * @return string
     */
    protected function _getInfoUrl()
    {
        return $this->_urlPrefix . $this->_infoUrl;
    }

    /**
     * @return string
     */
    public function getInfoDateConfigPath()
    {
        return 'ecommerceteam/' . $this->_getExtensionName() . '/last_update';
    }

    /**
     * @return string
     */
    public function getInstallConfigPath()
    {
        return 'ecommerceteam/' . $this->_getExtensionName() . '/install';
    }

    /**
     * @return bool|string
     */
    public function getInstallId()
    {
        /** @var $configCollection Mage_Core_Model_Resource_Config_Data_Collection */
        $configCollection = Mage::getResourceModel('core/config_data_collection');
        $configCollection->addFieldToFilter('path', array('eq' => $this->getInstallConfigPath()));
        $configCollection->addFieldToFilter('scope', array('eq' => 'default'));
        $configCollection->addFieldToFilter('scope_id', array('eq' => 0));
        $configModel = $configCollection->getFirstItem();
        if ($configModel) {
            return $configModel->getData('value');
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getLastUpdateDate()
    {
        /** @var $configCollection Mage_Core_Model_Resource_Config_Data_Collection */
        $configCollection = Mage::getResourceModel('core/config_data_collection');
        $configCollection->addFieldToFilter('path', array('eq' => $this->getInfoDateConfigPath()));
        $configCollection->addFieldToFilter('scope', array('eq' => 'default'));
        $configCollection->addFieldToFilter('scope_id', array('eq' => 0));
        $configModel = $configCollection->getFirstItem();
        if ($configModel) {
            return intval($configModel->getData('value'));
        } else {
            return 0;
        }
    }

    /**
     * @return int"null
     */
    protected function _makeInstall()
    {
        /** @var $helper Mage_Core_Helper_Data */
        $helper        = Mage::helper('core');
        $config        = Mage::app()->getConfig();
        $extensionName = $this->_getExtensionName();
        /** @var $orderId Mage_Core_Model_Config_Element */
        $orderId       = strval($config->getNode(sprintf('ecommerceteam/%s/order_id', $extensionName)));
        $purchaseId    = strval($config->getNode(sprintf('ecommerceteam/%s/purchase_id', $extensionName)));
        $installId     = null;
        $installPath   = $this->getInstallConfigPath();

        $info = array(
            'server_ip'      => $_SERVER['SERVER_ADDR'],
            'server_domain'  => $_SERVER['HTTP_HOST'],
            'user_ip'        => $_SERVER['REMOTE_ADDR'],
            'extension_name' => $extensionName,
            'order_id'       => $orderId,
            'purchase_id'    => $purchaseId,
            'platform'       => Mage::getVersion(),
        );

        try {
            $client     = new Zend_Http_Client($this->_getInstallUrl());
            $client->setParameterPost(array('info' => $info));
            $response   = $client->request(Zend_Http_Client::POST);
            $result     = new Varien_Object($helper->jsonDecode($response->getBody()));
            $installId  = $result->getData('install_id');

            if ($installId) {
                $this->updateConfig($installPath, $installId);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $installId;
    }

    /**
     * @param string $path
     * @param string $value
     */
    public function updateConfig($path, $value)
    {
        $config = Mage::app()->getConfig();
        /** @var $configModel Mage_Core_Model_Config_Data */
        $configModel = Mage::getModel('core/config_data');
        $configModel->load($path, 'path');
        $configModel->setData(
            array(
                'scope'    => 'default',
                'scope_id' => 0,
                'path'     => $path,
                'value'    => $value,
            )
        );
        $configModel->save();
        $config->loadDb();
    }

    /**
     * @return EcommerceTeam_Core_Model_Observer_Abstract
     */
    public function updateInfo()
    {
        /** @var $helper Mage_Core_Helper_Data */
        $helper      = Mage::helper('core');
        $installId   = $this->getInstallId();
        if (!$installId) {
            $installId = $this->_makeInstall();
        }
        if ($this->getLastUpdateDate() + (60*60*48) < time()) {
            try {
                $infoUrl  = $this->_getInfoUrl();
                $client   = new Zend_Http_Client($infoUrl);
                $client->setParameterGet(array('install_id' => $installId));
                $response = $client->request();
                $result   = new Varien_Object($helper->jsonDecode($response->getBody()));
                $messages = $result->getData('messages');
                if (is_array($messages)) {
                    foreach ($messages as $message) {
                        if (is_array($message)
                            && isset($message['title'], $message['description'])) {
                            /** @var $notification Mage_AdminNotification_Model_Inbox */
                            $notification = Mage::getModel('adminnotification/inbox');
                            if (!isset($message['severity']) || !in_array($message['severity'], $notification->getSeverities())) {
                                $message['severity'] = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;
                            }
                            $notification->setData($message);
                            $notification->save();
                            unset($notification);
                        }
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $this->updateConfig($this->getInfoDateConfigPath(), time());
        }
        return $this;
    }
}
