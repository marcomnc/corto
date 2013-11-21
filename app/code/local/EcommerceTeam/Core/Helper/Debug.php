<?php
/**
 * @package:    Core
 * @category:   EcommerceTeam
 * @copyright:  Copyright 2012 EcommerceTeam Inc. (http://www.ecommerce-team.com)
 * @version:    1.0.0
 */

class EcommerceTeam_Core_Helper_Debug
    extends Mage_Core_Helper_Abstract
{
    /**
     * @return FirePhp
     */
    protected function getFirePhp()
    {
        return FirePhp::getInstance(true);
    }

    public function send()
    {
        return call_user_func_array(array($this->getFirePhp(), 'fb'), func_get_args());
    }

    /**
     * @param $object
     * @param string|null $label
     * @return mixed
     */
    public function log($object, $label = null)
    {
        return $this->send($object, $label, FirePhp::LOG);
    }

    /**
     * @param $object
     * @param string|null $label
     * @return mixed
     */
    public function error($object, $label = null)
    {
        return $this->send($object, $label, FirePhp::ERROR);
    }

    /**
     * @param $object
     * @param string|null $label
     * @return mixed
     */
    public function info($object, $label = null)
    {
        return $this->send($object, $label, FirePhp::INFO);
    }

    /**
     * @param $object
     * @param string|null $label
     * @return mixed
     */
    public function warning($object, $label = null)
    {
        return $this->send($object, $label, FirePhp::WARN);
    }
}
