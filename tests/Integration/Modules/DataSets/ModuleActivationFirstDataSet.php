<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules\DataSets;

class ModuleActivationFirstDataSet
{
    /**
     * Data provider case with 5 modules prepared and virtualnamespace_with_everything module activated
     *
     * @return array
     */
    static function caseFiveModulesPreparedActivatedWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'virtualnamespace_virtualnamespace_extending_1_class', 'with_2_templates', 'with_2_files',
                'extending_3_blocks', 'virtualnamespace_with_events',
            ),

            // module that will be activated
            'virtualnamespace_with_everything',

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxorder'   => 'virtualnamespace_extending_1_class/myorder&virtualnamespace_with_everything/myorder1&virtualnamespace_with_everything/myorder2&virtualnamespace_with_everything/myorder3',
                    'oxarticle' => 'virtualnamespace_with_everything/myarticle',
                    'oxuser'    => 'virtualnamespace_with_everything/myuser',
                ),
                'files'           => array(
                    'with_2_files'    => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                    'virtualnamespace_with_everything' => array(
                        'myexception'  => 'virtualnamespace_with_everything/core/exception/myexception.php',
                        'myconnection' => 'virtualnamespace_with_everything/core/exception/myconnection.php',
                    ),
                    'virtualnamespace_with_events'     => array(
                        'myevents' => 'virtualnamespace_with_events/files/myevents.php',
                    ),
                ),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
                'disabledModules' => array(),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                    'virtualnamespace_with_everything'  => array(
                        'order_special.tpl'    => 'virtualnamespace_with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'virtualnamespace_with_everything/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'virtualnamespace_extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'virtualnamespace_with_events'        => '1.0',
                    'virtualnamespace_with_everything'    => '1.0',
                ),
                'events'          => array(
                    'virtualnamespace_extending_1_class'  => null,
                    'with_2_templates'   => null,
                    'with_2_files'       => null,
                    'extending_3_blocks' => null,
                    'virtualnamespace_with_events'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                    'virtualnamespace_with_everything'    => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }

    /**
     * Data provider case with 1 module prepared and virtualnamespace_with_everything module activated
     *
     * @return array
     */
    static function caseOneModulePreparedActivatedWithEverything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'no_extending'
            ),

            // module that will be activated
            'virtualnamespace_with_everything',

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxarticle' => 'virtualnamespace_with_everything/myarticle',
                    'oxorder'   => 'virtualnamespace_with_everything/myorder1&virtualnamespace_with_everything/myorder2&virtualnamespace_with_everything/myorder3',
                    'oxuser'    => 'virtualnamespace_with_everything/myuser',
                ),
                'files'           => array(
                    'virtualnamespace_with_everything' => array(
                        'myexception'  => 'virtualnamespace_with_everything/core/exception/myexception.php',
                        'myconnection' => 'virtualnamespace_with_everything/core/exception/myconnection.php',
                    )
                ),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
                'disabledModules' => array(),
                'templates'       => array(
                    'virtualnamespace_with_everything' => array(
                        'order_special.tpl'    => 'virtualnamespace_with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'virtualnamespace_with_everything/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'no_extending'    => '1.0',
                    'virtualnamespace_with_everything' => '1.0',
                ),
                'events'          => array(
                    'no_extending'    => null,
                    'virtualnamespace_with_everything' => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }


    /**
     * Data provider case with 3 modules prepared and virtualnamespace_virtualnamespace_extending_3_classes_with_1_extension module activated
     *
     * @return array
     */
    static function caseThreeModulesPreparedActivatedExtendingThreeClassesWithOneExtension()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'virtualnamespace_extending_1_class',
                'virtualnamespace_virtualnamespace_extending_3_classes_with_1_extension', 'virtualnamespace_extending_3_classes'
            ),

            // module that will be activated
            'virtualnamespace_extending_1_class_3_extensions',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    'oxorder'   => '' .
                        'virtualnamespace_extending_1_class/myorder&virtualnamespace_virtualnamespace_extending_3_classes_with_1_extension/mybaseclass&' .
                        'virtualnamespace_extending_3_classes/myorder&virtualnamespace_extending_1_class_3_extensions/myorder1&' .
                        'virtualnamespace_extending_1_class_3_extensions/myorder2&virtualnamespace_extending_1_class_3_extensions/myorder3',
                    'oxarticle' => 'virtualnamespace_virtualnamespace_extending_3_classes_with_1_extension/mybaseclass&virtualnamespace_extending_3_classes/myarticle',
                    'oxuser'    => 'virtualnamespace_virtualnamespace_extending_3_classes_with_1_extension/mybaseclass&virtualnamespace_extending_3_classes/myuser',
                ),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'virtualnamespace_virtualnamespace_extending_3_classes_with_1_extension' => '1.0',
                    'virtualnamespace_extending_1_class'                    => '1.0',
                    'virtualnamespace_extending_3_classes'                  => '1.0',
                    'virtualnamespace_extending_1_class_3_extensions'       => '1.0',
                ),
                'events'          => array(
                    'virtualnamespace_virtualnamespace_extending_3_classes_with_1_extension' => null,
                    'virtualnamespace_extending_1_class'                    => null,
                    'virtualnamespace_extending_3_classes'                  => null,
                    'virtualnamespace_extending_1_class_3_extensions'       => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 7 modules prepared and no_extending module activated
     *
     * @return array
     */
    static function caseSevenModulesPreparedActivatedNoExtending()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'virtualnamespace_extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'virtualnamespace_with_everything', 'virtualnamespace_with_events'
            ),

            // module that will be activated
            'no_extending',

            // environment asserts
            array(
                'blocks'          => array(
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                    array('template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl'),
                    array('template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl'),
                ),
                'extend'          => array(
                    'oxorder'   => 'virtualnamespace_extending_1_class/myorder&virtualnamespace_with_everything/myorder1&virtualnamespace_with_everything/myorder2&virtualnamespace_with_everything/myorder3',
                    'oxarticle' => 'virtualnamespace_with_everything/myarticle',
                    'oxuser'    => 'virtualnamespace_with_everything/myuser',
                ),
                'files'           => array(
                    'with_2_files'    => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                    'virtualnamespace_with_everything' => array(
                        'myexception'  => 'virtualnamespace_with_everything/core/exception/myexception.php',
                        'myconnection' => 'virtualnamespace_with_everything/core/exception/myconnection.php',
                    ),
                    'virtualnamespace_with_events'     => array(
                        'myevents' => 'virtualnamespace_with_events/files/myevents.php',
                    ),
                ),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
                'disabledModules' => array(),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                    'virtualnamespace_with_everything'  => array(
                        'order_special.tpl'    => 'virtualnamespace_with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'virtualnamespace_with_everything/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'virtualnamespace_extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_settings'    => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'no_extending'       => '1.0',
                    'virtualnamespace_with_events'        => '1.0',
                    'virtualnamespace_with_everything'    => '1.0',
                ),
                'events'          => array(
                    'virtualnamespace_extending_1_class'  => null,
                    'with_2_templates'   => null,
                    'with_2_settings'    => null,
                    'with_2_files'       => null,
                    'extending_3_blocks' => null,
                    'no_extending'       => null,
                    'virtualnamespace_with_events'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                    'virtualnamespace_with_everything'    => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }

    /**
     * Data provider case with 1 module prepared and with_2_files module activated
     *
     * @return array
     */
    static function caseOneModulePreparedActivatedWithTwoFiles()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'no_extending'
            ),

            // module that will be activated
            'with_2_files',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(
                    'with_2_files' => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                ),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending' => '1.0',
                    'with_2_files' => '1.0',
                ),
                'events'          => array(
                    'no_extending' => null,
                    'with_2_files' => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 1 module prepared and with_2_settings module activated
     *
     * @return array
     */
    static function caseOneModulePreparedActivatedWithTwoSettings()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'no_extending'
            ),

            // module that will be activated
            'with_2_settings',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name'),
                ),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending'    => '1.0',
                    'with_2_settings' => '1.0',
                ),
                'events'          => array(
                    'no_extending'    => null,
                    'with_2_settings' => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 1 module prepared and with_2_templates module activated
     *
     * @return array
     */
    static function caseOneModulePreparedActivatedWithTwoTemplates()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'no_extending'
            ),

            // module that will be activated
            'with_2_templates',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'no_extending'     => '1.0',
                    'with_2_templates' => '1.0',
                ),
                'events'          => array(
                    'no_extending'     => null,
                    'with_2_templates' => null,
                ),
            )
        );
    }
}
