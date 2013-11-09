<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer group edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MpsSistemi_Iplocation_Block_Adminhtml_Zone_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'mpslocation';
        $this->_controller = 'adminhtml_zone_group';        
        parent::__construct();
        $this->setSaveParametersInSession(true);

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('mpslocation')->__('Salva Gruppo'));
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('mpslocation')->__('Cancella Gruppo'));
        } else {
            $this->_removeButton('delete');
        }

    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('mpslocation_zone_group')->getEntityId()) {
            return Mage::helper('mpslocation')->__("Modifica Gruppo '%s'", $this->htmlEscape(Mage::registry('mpslocation_zone_group')->getGoupName()));
        }
        else {
            return Mage::helper('mpslocation')->__('Nuovo Gruppo');
        }
    }

    /**
     * Check permission for passed action
     * @toDo
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        //return Mage::getSingleton('admin/session')->isAllowed('mps/mpspricezone/' . $action);
        return true;
    }
}
