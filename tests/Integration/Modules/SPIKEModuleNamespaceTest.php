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


class SPIKETestUtilsObject extends \OxidEsales\EshopCommunity\Core\UtilsObject
{
    public function getTheModuleChainsGenerator() {
        return $this->getModuleChainsGenerator();
    }

    public function getTheClassNameProvider() {
        return $this->getClassNameProvider();
    }
}

class SPIKEModuleNamespaceTest extends BaseModuleTestCase
{
    const TEST_PRICE = 10.0;

    protected function setUp()
    {
        parent::setUp();

        $this->getConfig()->setConfigParam('blDoNotDisableModuleOnError', true);
        $this->assertPrice();
    }

    /**
     * @return array
     */
    public function providerModuleCacheIssueOnDeletion()
    {
        return array(
            $this->caseTwoModulesNamespacedLast(),
            #$this->caseTwoModulesNamespacedFirst(),
        );
    }

    /**
     * Tests if module was activated and then properly deactivated.
     *
     * @group module
     *
     * @dataProvider providerModuleCacheIssueOnDeletion
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param string $moduleId
     * @param array  $resultToAsserts (array key 0 -> before, array key 1 -> after case)
     * @param array  $priceAsserts
     */
    public function testModuleCacheIssue($installModules, $moduleName, $moduleId, $resultToAsserts, $priceAsserts)
    {
        $environment = new Environment();
        $environment->prepare($installModules);

        $module = oxNew('oxModule');
        $module->load($moduleName);
        $this->deactivateModule($module, $moduleId);
        $this->activateModule($module, $moduleId);
        $this->runAsserts($resultToAsserts[0]);

        # information should not not only be in the database but also in the file cache, so let's check this:
        # config.1.adisabledmodules.txt
        # config.1.amodulefiles.txt
        # config.1.amodules.txt
        # NOTE: file cache is filled a different times, 'amodulefiles' and 'adisabledmodules' are only generated
        #       when oxPrice object is created here

        $subShopSpecificCache = $this->getFileCache();
        $this->assertEquals($resultToAsserts[0]['extend'], $subShopSpecificCache->getFromCache('amodules'));
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));

        # When we create an oxPrice object, some more caches get filled (whenever oxNew is called)
        oxNew('oxPrice');
        $this->assertEquals($resultToAsserts[0]['extend'], $subShopSpecificCache->getFromCache('amodules'));
        #$this->assertEquals($resultToAsserts[0]['files'], $subShopSpecificCache->getFromCache('amodulefiles'));
        #$this->assertEquals($resultToAsserts[0]['disabledModules'], $subShopSpecificCache->getFromCache('adisabledmodules'));

        # Deactivating a module via shop admin means: the module id is marked as disabled and module deactivation event
        # is called if any exists (which is not the case here).
        # Now deactivate the module and check what's left in cache and database
        $this->deactivateModule($module, $moduleId); //this is done via module installer

        #NOTE: moduleInstaller also cleans the moduleCache which in turn calls ModuleVariablesLocator::resetModuleVariables();
        #      and this cleans the file cache.
        $this->assertNull($subShopSpecificCache->getFromCache('amodules'));
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->runAsserts($resultToAsserts[1]);

        #Trick the shop into thinking the module was deleted. Caches are empty, change data directly in database.
        #NOTE: using shop to store changes in database triggers refill of amodules cache.
        $newAModules = $this->removeModule();

        # Accessing the admin modulelist controller refills the file cache for 'amodules'
        # because the controller calls oxModuleList::getModulesDir(
        $moduleList = oxNew('OxidEsales\Eshop\Core\Module\ModuleList');
        $moduleList->getModulesFromDir($this->getConfig()->getModulesDir());
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->assertEquals($newAModules, $subShopSpecificCache->getFromCache('amodules'));

        $expectedDeletedExtension = array('EshopTestModuleNone' => array('files' => array('EshopTestModuleNone/metadata.php')));
        $this->assertEquals($expectedDeletedExtension, $moduleList->getDeletedExtensions());
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->assertEquals($newAModules, $subShopSpecificCache->getFromCache('amodules'));

        # run cleanup on module list
        $moduleList->cleanup();
        $this->runAsserts($resultToAsserts[2]);
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->assertNull($subShopSpecificCache->getFromCache('amodules'));

        # run ModuleList::getModulesFromDir again
        $moduleList->getModulesFromDir($this->getConfig()->getModulesDir());
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->assertEquals($resultToAsserts[2]['extend'], $subShopSpecificCache->getFromCache('amodules'));
    }

    /**
     * Data provider case for namespaced module
     *
     * @return array
     */
    protected function caseTwoModulesNamespacedFirst()
    {
        $environmentAssertsWithModulesActive = array(
            'blocks'          => array(),
            'extend'          => array(
                \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModule\Application\Controller\TestModuleOnePaymentController::class,
                \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice::class,
                'payment' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                'oxprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice'
            ),
            'files' => array(
                'without_own_module_namespace' =>
                    array('testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
                          'testmoduletwopaymentcontroller' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController.php',
                          'testmoduletwoprice'             => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice.php'
                    ),
                'EshopTestModuleOne'           => array()),
            'settings' => array(),
            'disabledModules' => array(),
            'templates'       => array(),
            'versions'        => array(
                'without_own_module_namespace' => '1.0.0',
                'EshopTestModuleOne' => '1.0.0'
            ),
            'events'          => array('without_own_module_namespace' => null, 'EshopTestModuleOne' => null)
        );

        $environmentAssertsAfterDeactivation = $environmentAssertsWithModulesActive;
        $environmentAssertsAfterDeactivation['files'] = array(
        'without_own_module_namespace' =>
            array('testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
                  'testmoduletwopaymentcontroller' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController.php',
                  'testmoduletwoprice'             => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice.php'
            )
        );
        $environmentAssertsAfterDeactivation['versions'] = array('without_own_module_namespace' => '1.0.0');
        $environmentAssertsAfterDeactivation['events'] = array('without_own_module_namespace' => null);
        $environmentAssertsAfterDeactivation['disabledModules'] = array('EshopTestModuleOne');

        $priceAssertsWihModulesActive = array('factor' => 2 * 3,
                                              'class'  => 'OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice');

        $priceAssertsAfterDeactivation = array('factor' => 3,
                                               'class'  => 'TestModuleTwoPrice');

        return array(

            // modules to be activated during test preparation
            array('with_own_module_namespace',
                  'without_own_module_namespace'),

            // module that will be activated/deactivated
            'with_own_module_namespace',

            /// module id
            'EshopTestModuleOne',

            // environment asserts
            array($environmentAssertsWithModulesActive,
                  $environmentAssertsAfterDeactivation
            ),

            // price multiplier
            array($priceAssertsWihModulesActive,
                  $priceAssertsAfterDeactivation)
        );
    }

    /**
     * Data provider case for namespaced module
     *
     * @return array
     */
    protected function caseTwoModulesNamespacedLast()
    {
        $environmentAssertsWithModulesActive = array(
            'blocks'          => array(),
            'extend'          => array(
                'payment' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                'oxprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
                \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModule\Application\Controller\TestModuleOnePaymentController::class,
                \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice::class
            ),
            'files' => array(
                'EshopTestModuleOne'           => array(),
                'without_own_module_namespace' =>
                    array('testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
                          'testmoduletwopaymentcontroller' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController.php',
                          'testmoduletwoprice'             => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice.php'
                    )
               ),
            'settings' => array(),
            'disabledModules' => array(),
            'templates'       => array(),
            'versions'        => array(
                'EshopTestModuleOne' => '1.0.0',
                'without_own_module_namespace' => '1.0.0',
            ),
            'events'          => array('EshopTestModuleOne' => null, 'without_own_module_namespace' => null)
        );

        $environmentAssertsAfterDeactivation = $environmentAssertsWithModulesActive;
        $environmentAssertsAfterDeactivation['files'] = array(
            'without_own_module_namespace' =>
                array('testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
                      'testmoduletwopaymentcontroller' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController.php',
                      'testmoduletwoprice'             => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice.php'
                )
        );
        $environmentAssertsAfterDeactivation['versions'] = array('without_own_module_namespace' => '1.0.0');
        $environmentAssertsAfterDeactivation['events'] = array('without_own_module_namespace' => null);
        $environmentAssertsAfterDeactivation['disabledModules'] = array('EshopTestModuleOne');

        $environmentAssertsAfterCleanup = $environmentAssertsAfterDeactivation;
        unset($environmentAssertsAfterCleanup['disabledModules']);
        $environmentAssertsAfterCleanup['extend'] = array(
        'payment' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
        'oxprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
        );

        $priceAssertsWihModulesActive = array('factor' => 2 * 3,
                                              'class'  => 'OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice');

        $priceAssertsAfterDeactivation = array('factor' => 3,
                                               'class'  => 'TestModuleTwoPrice');

        return array(

            // modules to be activated during test preparation
            array('without_own_module_namespace',
                  'with_own_module_namespace'),

            // module that will be activated/deactivated
            'with_own_module_namespace',

            /// module id
            'EshopTestModuleOne',

            // environment asserts
            array($environmentAssertsWithModulesActive,
                  $environmentAssertsAfterDeactivation,
                  $environmentAssertsAfterCleanup
            ),

            // price multiplier
            array($priceAssertsWihModulesActive,
                  $priceAssertsAfterDeactivation)
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
        $price->setPrice(self::TEST_PRICE);

        // check for module price class
        if (isset($asserts['class'])) {
            $this->assertTrue(is_a($price, $asserts['class']), 'Price object class not as expected ' . get_class($price));
        }

        $this->assertEquals($factor * self::TEST_PRICE, $price->getPrice(), 'Price not as expected.');
        return $price;
    }

    /**
     * Change module info in oxconfig to trick shop into thinking module was deleted.
     *
     * @return array
     */
    private function removeModule()
    {
        $modules = array(
            'payment' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
            'oxprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
            \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModuleNone\Application\Controller\TestModuleNonePaymentController::class,
            \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopTestModuleNone\Application\Model\TestModuleNonePrice::class
        );

        $extensions = array(
            'without_own_module_namespace' => array('without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                                                    'without_own_module_namespace/Application/Model/TestModuleTwoPrice'),
            'EshopTestModuleNone' => array('OxidEsales\EshopTestModuleNone\Application\Controller\TestModuleNonePaymentController',
                                           'OxidEsales\EshopTestModuleNone\Application\Model\TestModuleNonePrice')
        );

        $disabledModules = array('EshopTestModuleNone');

        $this->getConfig()->saveShopConfVar('aarr', 'aModules', $modules);
        $this->getConfig()->saveShopConfVar('aarr', 'aModuleExtensions', $extensions);
        $this->getConfig()->saveShopConfVar('arr', 'aDisabledModules', $disabledModules);

        $subShopSpecificCache = $this->getFileCache();
        $subShopSpecificCache->clearCache();
        $this->assertNull($subShopSpecificCache->getFromCache('amodules'));
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));

        return $modules;
    }

    /**
     * Get a file cache object
     */
    private function getFileCache()
    {
        $shopIdCalculatorMock = $this->getMock('\OxidEsales\EshopCommunity\Core\ShopIdCalculator', array('getShopId'), array(), '', false);
        $shopIdCalculatorMock->expects($this->any())->method('getShopId')->will($this->returnValue(1));

        $subShopSpecificCache = oxNew('\OxidEsales\EshopCommunity\Core\SubShopSpecificFileCache', $shopIdCalculatorMock);

        return $subShopSpecificCache;
    }
}
