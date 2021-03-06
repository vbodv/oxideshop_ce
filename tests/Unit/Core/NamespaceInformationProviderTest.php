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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Test \OxidEsales\Eshop\Core\NamespaceInformationProvider.
 * Class NamespaceInformationProviderTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core
 */
class NamespaceInformationProviderTest extends UnitTestCase
{
    /**
     * Test getter for shop edition namespaces.
     */
    public function testGetShopEditionNamespaces()
    {
        $expected = ['CE' => 'OxidEsales\EshopCommunity\\',
                     'PE' => 'OxidEsales\EshopProfessional\\',
                     'EE' => 'OxidEsales\EshopEnterprise\\'];
        $this->assertEquals($expected, \OxidEsales\Eshop\Core\NamespaceInformationProvider::getShopEditionNamespaces());
    }

    /**
     * Test getter for virtual namespaces.
     */
    public function testGetVirtualNamespace()
    {
        $this->assertEquals('OxidEsales\Eshop\\', \OxidEsales\Eshop\Core\NamespaceInformationProvider::getVirtualNamespace());
    }

    /**
     * Test method isNamespacedClass.
     */
    public function testIsNamespacedClass()
    {
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::isNamespacedClass(\OxidEsales\Eshop\Application\Model\Article::class));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::isNamespacedClass('oxArticle'));
    }

    /**
     * Test method classBelongsToShopVirtualNamespace.
     */
    public function testClassBelongsToShopVirtualNamespace()
    {
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopVirtualNamespace('oxArticle'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopVirtualNamespace('oxarticle'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopVirtualNamespace(\OxidEsales\EshopCommunity\Application\Model\Article::class));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopVirtualNamespace('OxidEsales\EshopCommunity\Application\Model\Article'));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopVirtualNamespace('OxidEsales\Eshop\Application\Model\Article'));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopVirtualNamespace(\OxidEsales\Eshop\Application\Model\Article::class));
    }

    /**
     * Test method classBelongsToShopEditionNamespace.
     */
    public function testClassBelongsToShopEditionNamespace()
    {
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace('oxArticle'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace('oxarticle'));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace(\OxidEsales\EshopCommunity\Application\Model\Article::class));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace('OxidEsales\EshopCommunity\Application\Model\Article'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace('OxidEsales\Eshop\Application\Model\Article'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace(\OxidEsales\Eshop\Application\Model\Article::class));
    }

    /**
     * Test method classBelongsToShopEditionNamespace.
     */
    public function testClassBelongsToShopEditionTestNamespace()
    {
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionTestNamespace('oxArticle'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionTestNamespace('oxarticle'));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionTestNamespace(\OxidEsales\EshopCommunity\Tests\Unit\Core\NamespaceInformationProviderTest::class));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionTestNamespace('OxidEsales\EshopCommunity\Tests\Unit\Core\NamespaceInformationProviderTest'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionTestNamespace('OxidEsales\Eshop\Application\Model\Article'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionTestNamespace(\OxidEsales\Eshop\Application\Model\Article::class));
    }
}
