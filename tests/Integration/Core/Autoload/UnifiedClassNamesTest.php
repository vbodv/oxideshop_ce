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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload;


class UnifiedClassNamesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * In many modules the method get_class() is used to detect if the objects class is of a e.g. type
     * Article. Module developers should create instances with the unified namespace classes (not edition
     * classes) and get back the unified namespace class from the method get_class().
     *
     * @dataProvider dataProviderTestGetClassName
     *
     * @param object $object            An object to be tested
     * @param string $expectedClassName Expected class name of the given object
     * @param string $message           Assertion message
     */
    public function testGetClassName($object, $expectedClassName, $message)
    {
        $actualClassName = get_class($object);
        $this->assertEquals(
            $expectedClassName,
            $actualClassName,
            $message
        );
    }

    public function dataProviderTestGetClassName()
    {
        return [
            [
                'object'            => new \OxidEsales\Eshop\Application\Model\Article(),
                'expectedClassName' => \OxidEsales\Eshop\Application\Model\Article::class,
                'message'           => 'Calling get_class on an instance of \OxidEsales\Eshop\Application\Model\Article ' .
                                       'created with new returns \OxidEsales\Eshop\Application\Model\Article::class'
            ],
            [
                'object'            => oxNew(\OxidEsales\Eshop\Application\Model\Article::class),
                'expectedClassName' => \OxidEsales\Eshop\Application\Model\Article::class,
                'message'           => 'Calling get_class on an instance of \OxidEsales\Eshop\Application\Model\Article ' .
                                       'created with oxNew returns \OxidEsales\Eshop\Application\Model\Article::class'
            ],
            [
                'object'            => new \oxArticle(),
                'expectedClassName' => \OxidEsales\Eshop\Application\Model\Article::class,
                'message'           => 'Calling get_class on an instance of oxArticle ' .
                                       'created with new returns \OxidEsales\Eshop\Application\Model\Article::class'
            ],
            [
                'object'            => oxNew('oxArticle'),
                'expectedClassName' => \OxidEsales\Eshop\Application\Model\Article::class,
                'message'           => 'Calling get_class on an instance of oxArticle ' .
                                       'created with oxNew returns \OxidEsales\Eshop\Application\Model\Article::class'
            ],
        ];
    }
}
