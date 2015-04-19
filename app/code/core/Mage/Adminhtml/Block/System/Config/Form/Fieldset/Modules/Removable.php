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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_Removable
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        $config = Mage::getConfig()->loadModulesConfiguration('uninstall.xml');

        if ($config->getNode('modules')) {
            $modules = array_keys((array)$config->getNode('modules')->children());

            $dispatchResult = new Varien_Object($modules);
            Mage::dispatchEvent(
                'adminhtml_system_config_removable_modules_render_before',
                array('modules' => $dispatchResult)
            );
            $modules = $dispatchResult->toArray();

            sort($modules);

            foreach ($modules as $moduleName) {
                if ($moduleName==='Mage_Adminhtml') {
                    continue;
                }
                $html.= $this->_getFieldHtml($element, $moduleName);
            }
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(array('show_in_default'=>1, 'show_in_website'=>0));
        }
        return $this->_dummyElement;
    }

    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    protected function _getFieldHtml($fieldset, $moduleName)
    {
        $dummyElement = $this->_getDummyElement();

        $field = $fieldset->addField($moduleName, 'checkbox',
            array(
                'name'                  => 'groups[removable_modules][fields]['.$moduleName.'][value]',
                'after_element_html'    => sprintf('<label for="%1$s">%1$s</label>', $moduleName),
                'value'                 => '1',
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($dummyElement),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($dummyElement),
            ))->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
