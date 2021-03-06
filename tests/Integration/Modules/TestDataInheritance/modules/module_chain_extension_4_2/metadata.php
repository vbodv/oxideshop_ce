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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'module_chain_extension_4_2',
    'title'       => 'Test plain module class chain extension of namespaced module class 4.2',
    'description' => 'This module has the non namespaced class in it',
    'thumbnail'   => 'picture.png',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'files'       => ['module_chain_extension_4_2_myclass' => 'module_chain_extension_4_2/module_chain_extension_4_2_myclass.php'],
    'extend'      => [\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension42\MyClass42::class => 'module_chain_extension_4_2/module_chain_extension_4_2_myclass']
);
