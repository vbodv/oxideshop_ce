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

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\Eshop\Application\Controller\ContentController;
use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;
use OxidEsales\TestingLibrary\ServiceCaller;
use OxidEsales\TestingLibrary\Services\Files\Remove;
use OxidEsales\TestingLibrary\Services\Library\FileHandler;


class testModuleList extends \OxidEsales\EshopCommunity\Core\Module\ModuleList
{
     public function getTheInvalidExtensions($sModuleId)
     {
         /*
         $extendedClasses = $this->getModuleExtensions($sModuleId);
         $deletedExtensions = array();

         foreach ($extendedClasses as $oxidEshopClass => $moduleClasses) {
             foreach ($moduleClasses as $sModulePath) {
                 if (!$this->isNamespacedClass($sModulePath)) {
                     $completeExtensionPath = $this->getConfig()->getModulesDir() . $sModulePath . '.php';

                     if (!file_exists($completeExtensionPath)) {
                         $deletedExtensions[$oxidEshopClass][] = $sModulePath;
                     }
                 } else {
                     if (!class_exists($sModulePath, false)) {
                         $deletedExtensions[$oxidEshopClass][] = $sModulePath;
                     }
                 }
             }
         }

         return $deletedExtensions;
         */
     }

}


/**
 * Module functionality functionality.
 *
 * @group module
 */
class ModuleNamespaceTest extends ModuleBaseTest
{
    const TEST_MODULE_NAMESPACE = 'with_own_module_namespace';
    const TEST_MODULE_OLDSTYLE = 'without_own_module_namespace';

    const TITLE_MODULE_NAMESPACE = 'Test module #9 - namespaced';
    const TITLE_MODULE_OLDSTYLE = 'Test module #10 - not namespaced';

    const ID_MODULE_NAMESPACE = 'EshopAcceptanceTestModuleNine';
    const ID_MODULE_OLDSTYLE = 'EshopAcceptanceTestModuleTen';

    const TEST_ARTICLE_OXID = 'f4f73033cf5045525644042325355732'; // '/en/Special-Offers/Transport-container-BARREL.html'

    /**
     * Set up
     */
    protected function setUp()
    {
        parent::setUp();

        //TODO: check if test works for subshop as well (which login to use, do we need to provide shopid somewhere ...)
        $testConfig = $this->getTestConfig();
        if ($testConfig->isSubShop()) {
            $this->markTestSkipped('Test is not for SubShop');
        }
    }

    /**
     * Physically remove an activated module from shop without deactivating it.
     * Shop should detect this and request cleanup in shop admin backend.
     */
    public function testPhysicallyDeleteNamespacedModuleWithoutDeactivation()
    {
        $this->markTestIncomplete('WIP'):

        $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TITLE_MODULE_OLDSTYLE);
       # $this->assertNoProblem();
       # $this->checkFrontend(3 * 3); // price multiplies more than expected, some flaw in module

       # $this->loginAdmin('Extensions', 'Modules');
        $this->activateModule(self::TITLE_MODULE_NAMESPACE);
       # $this->assertNoProblem();
       # $this->checkFrontend(3 * 3 * 2 * 2); // price multiplies more than expected, some flaw in module


        $test = oxNew('OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testModuleList');
        var_dump($test->getModuleExtensions(self::ID_MODULE_NAMESPACE));

        /*
        $this->checkFrontend(3 * 3 * 2 * 2); // price multiplies more than expected, some flaw in module

        $this->deleteModule(self::TEST_MODULE_NAMESPACE);

        $this->loginAdmin('Extensions', 'Modules');
        $this->frame('edit');
        $this->assertTextPresent('Problematic Files');
        $this->assertTextPresent('EshopTestModuleOne/metadata.php');
        $this->clickAndWait('yesButton');

        $this->checkFrontend(3 * 3); // price multiplies more than expected, some flaw in module
*/
    }

    /**
     * Test modules affect the frontend price.
     *
     * @param integer $factor
     */
    protected function checkFrontend($factor = 1)
    {
        $this->openShop();
        $this->openArticle(self::TEST_ARTICLE_OXID, true);

        $standardPrice = 24.95 * $factor;
        $standardPrice = str_replace('.', ',', $standardPrice);
        $this->assertTextPresent($standardPrice);
    }

    /**
     * Check for problematic extensions
     */
    protected function assertNoProblem()
    {
        $this->selectMenu('Extensions', 'Modules');
        $this->frame('edit');
        #$this->assertTextNotPresent('Problematic Files');
    }

}
