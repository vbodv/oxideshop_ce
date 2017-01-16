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

class ModuleNamespaceTest extends BaseModuleTestCase
{
    const TEST_ARTICLE_OXID = '1126';

    /**
     * @return array
     */
    public function providerModuleActivation()
    {
        return array(
            $this->caseModuleNamespace()
        );
    }

    /**
     * Tests if module was activated.
     *
     * @group module
     *
     * @dataProvider providerModuleActivation
     *
     * @param array  $aInstallModules
     * @param string $sModule
     * @param array  $aResultToAsserts
     */
    public function testModuleWorksAfterActivation($aInstallModules, $sModule, $aResultToAsserts)
    {
        $article = oxNew('oxArticle');
        $article->load(self::TEST_ARTICLE_OXID);
        $this->assertEquals(34.0, $article->getPrice()->getBruttoPrice());
        $this->assertTrue(class_exists('OxidEsales\EshopTestModule\Application\Model\TestModuleOneModel'));

        $environment = new Environment();
        $environment->prepare($aInstallModules);

        $module = oxNew('oxModule');
        $module->load($sModule);
        $this->deactivateModule($module);
        $this->activateModule($module);

        $this->runAsserts($aResultToAsserts);

        $article = oxNew('oxArticle');
        $article->load(self::TEST_ARTICLE_OXID);
        $this->assertEquals(68.0, $article->getPrice()->getBruttoPrice());
    }

    /**
     * Tests if module was activated.
     *
     * @group module
     *
     * @dataProvider providerModuleActivation
     *
     * @param array  $aInstallModules
     * @param string $sModule
     * @param array  $aResultToAsserts
     */
    public function testModuleDeactivation($aInstallModules, $sModule, $aResultToAsserts)
    {
        $article = oxNew('oxArticle');
        $article->load(self::TEST_ARTICLE_OXID);
        $this->assertEquals(34.0, $article->getPrice()->getBruttoPrice());
        $this->assertTrue(class_exists('OxidEsales\EshopTestModule\Application\Model\TestModuleOneModel'));

        $environment = new Environment();
        $environment->prepare($aInstallModules);

        $module = oxNew('oxModule');
        $module->load($sModule);
        $this->deactivateModule($module);
        $this->activateModule($module);
        $this->runAsserts($aResultToAsserts);

        $this->deactivateModule($module);

        $article = oxNew('oxArticle');
        $article->load(self::TEST_ARTICLE_OXID);
        $this->assertEquals(34.0, $article->getPrice()->getBruttoPrice());
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
            )
        );
    }

}
