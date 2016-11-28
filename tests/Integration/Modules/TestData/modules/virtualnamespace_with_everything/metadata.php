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

/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'virtualnamespace_with_everything',
    'title'        => 'Test extending 1 shop class',
    'description'  => 'Module testing extending 1 shop class',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => array(
        \OxidEsales\Eshop\Application\Model\Article::class => \virtualnamespace_with_everything\MyArticle::class,
        \OxidEsales\Eshop\Application\Model\Order::class => array(
            \virtualnamespace_with_everything\MyOrder1::class,
            \virtualnamespace_with_everything\MyOrder2::class,
            \virtualnamespace_with_everything\MyOrder3::class,
        ),
        \OxidEsales\Eshop\Application\Model\User::class => \virtualnamespace_with_everything\MyUser::class,
    ),
    'blocks' => array(
        array('template' => 'page/checkout/basket.tpl',  'block'=>'basket_btn_next_top',    'file'=>'/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/payment.tpl', 'block'=>'select_payment',         'file'=>'/views/blocks/page/checkout/mypaymentselector.tpl'),
    ),
    'events'       => array(
        'onActivate'   => \virtualnamespace_with_everything\MyEvents::onActivate(),
        'onDeactivate' => \virtualnamespace_with_everything\MyEvents::onDeactivate()
    ),
    'templates' => array(
        'order_special.tpl'      => 'virtualnamespace_with_everything/views/admin/tpl/order_special.tpl',
        'user_connections.tpl'   => 'virtualnamespace_with_everything/views/tpl/user_connections.tpl',
    ),
    'files' => array(
        'myexception'  => 'virtualnamespace_with_everything/core/exception/myexception.php',
        'myconnection' => 'virtualnamespace_with_everything/core/exception/myconnection.php',
    ),
    'settings' => array(
        array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
        array('group' => 'my_displayname',  'name' => 'sDisplayName',   'type' => 'str',  'value' => 'Some name'),
    ),
);
