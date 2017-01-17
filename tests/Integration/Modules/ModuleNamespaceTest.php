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
namespace Integration\Modules;

require_once __DIR__ . '/BaseModuleTestCase.php';

use oxUtilsObject;

class ModuleNamespaceTest extends BaseModuleTestCase
{
    const TEST_ARTICLE_OXID = '1126';
    const TEST_ARTICLE_DEFAULT_PRICE = 34.0;

    protected function setUp()
    {
        parent::setUp();

        $this->getConfig()->setConfigParam('blDoNotDisableModuleOnError', true);
        $this->assertPrice();
    }

    /**
     * @return array
     */
    public function providerModuleActivation()
    {
        return array(
            $this->caseNoModuleNamespace(),
            #$this->caseModuleNamespace()
        );
    }

    /**
     * Tests if module was activated.
     *
     * @group module
     *
     * @dataProvider providerModuleActivation
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param array  $resultToAsserts
     * @param array  $priceAsserts
     */
    public function _testModuleWorksAfterActivation($installModules, $moduleName, $resultToAsserts, $priceAsserts)
    {
        $environment = new Environment();
        $environment->prepare($installModules);

        $module = oxNew('oxModule');
        $module->load($moduleName);
        $this->deactivateModule($module);
        $this->activateModule($module);

        $this->runAsserts($resultToAsserts);
        $this->assertPrice($priceAsserts);
    }

    /**
     * Tests if module was activated and then properly deactivated.
     *
     * @group module
     *
     * @dataProvider providerModuleActivation
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param array  $resultToAsserts
     * @param array  $priceAsserts
     */
    public function testModuleDeactivation($installModules, $moduleName, $resultToAsserts, $priceAsserts)
    {
        $environment = new Environment();
        $environment->prepare($installModules);

        $module = oxNew('oxModule');
        $module->load($moduleName);
        $this->deactivateModule($module);
        $this->activateModule($module);

        $this->runAsserts($resultToAsserts);
        $this->assertPrice($priceAsserts);

        $this->deactivateModule($module);
       # oxUtilsObject::resetClassInstances();


        $price = oxNew('oxPrice');

        $price = $this->assertPrice($priceAsserts);
        $this->assertFalse(is_a($price, $priceAsserts['class']), 'Price object class not as expected');
    }

    /**
     * Data provider case for namespaced module
     *
     * @return array
     */
    protected function caseModuleNamespace()
    {
        return array(

            // modules to be activated during test preparation
            array('with_own_module_namespace'),

            // module that will be reactivated
            'with_own_module_namespace',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModule\Application\Controller\TestModuleOnePaymentController::class,
                    \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice::class
                ),
                'files'           => array('EshopTestModuleOne' => array()),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'EshopTestModuleOne' => '1.0.0'
                ),
                'events'          => array('EshopTestModuleOne' => null)
            ),

            // price multiplier
            array('factor' => 2,
                  'class'  => 'TestModuleOnePrice')
        );
    }

    /**
     * Data provider case for not namespaced module
     *
     * @return array
     */
    protected function caseNoModuleNamespace()
    {
        return array(

            // modules to be activated during test preparation
            array('without_own_module_namespace'),

            // module that will be reactivated
            'without_own_module_namespace',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                   'payment' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                   'oxprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice'
                ),
                'files'           => array(
                    'EshopTestModuleTwo' => array(
                        'testmoduletwomodel' => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
                        'testmoduletwopaymentcontroller' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController.php',
                        'testmoduletwoprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice.php')
                ),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'EshopTestModuleTwo' => '1.0.0'
                ),
                'events'          => array('EshopTestModuleTwo' => null)
            ),

            array('factor' => 3,
                  'class'  => 'TestModuleTwoPrice')
        );
    }

    /**
     * Check test article's price. Module multiplies the price by factor.
     *
     * @param array $asserts
     *
     * @return oxPrice
     */
    private function assertPrice($asserts = array())
    {
        $factor = isset($asserts['factor']) ? $asserts['factor'] : 1;

        $price = oxNew('oxprice');
        $price->setPrice(self::TEST_ARTICLE_DEFAULT_PRICE);

        var_dump($price->getPrice());

        /*
        $article = oxNew('oxArticle');
        $article->load(self::TEST_ARTICLE_OXID);
        $price = $article->getPrice();

        // check for module price class
        if (isset($asserts['class'])) {
            $this->assertTrue(is_a($price, $asserts['class']), 'Price object class not as expected');
        }

        $this->assertEquals($factor * self::TEST_ARTICLE_DEFAULT_PRICE, $price->getBruttoPrice(), 'Test article price not as expected.');
*/
        return $price;
    }
}
