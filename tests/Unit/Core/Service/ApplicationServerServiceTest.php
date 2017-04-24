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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * @covers \OxidEsales\Eshop\Core\Service\ApplicationServerService
 */
class ApplicationServerServiceTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testLoadList()
    {
        $appServerDao = $this->getApplicationServerDaoMock("findAll", true);

        $service = $this->getApplicationServerServiceMock($appServerDao);

        $this->assertTrue($service->loadList());
    }

    public function testDelete()
    {
        $id = 'testId';

        $appServerDao = $this->getApplicationServerDaoMock("delete", $id);

        $service = $this->getApplicationServerServiceMock($appServerDao);

        $this->assertEquals($id, $service->deleteById($id));
    }

    public function testLoad()
    {
        $id = 'testId';

        $appServerDao = $this->getApplicationServerDaoMock("findById", $id);

        $service = $this->getApplicationServerServiceMock($appServerDao);

        $this->assertEquals($id, $service->load($id));
    }

    public function testSaveIfExists()
    {
        $id = 'testId';
        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = $this->getMock(
            \OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class,
            array("findById", "update"),
            array($databaseProvider, $config));
        $appServerDao->expects($this->once())->method('findById')->will($this->returnValue($id));
        $appServerDao->expects($this->once())->method('update')->will($this->returnValue($id));

        $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
        $server->setId('testId');
        $server->setTimestamp('timestamp');
        $server->setIp('127.0.0.1');
        $server->setLastFrontendUsage('frontendUsageTimestamp');
        $server->setLastAdminUsage('adminUsageTimestamp');
        $server->setIsValid();

        $service = $this->getApplicationServerServiceMock($appServerDao);

        $this->assertEquals($id, $service->save($server));
    }
    public function testSaveNewElement()
    {
        $id = 'testId';
        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = $this->getMock(
            \OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class,
            array("findById", "insert"),
            array($databaseProvider, $config));
        $appServerDao->expects($this->once())->method('findById')->will($this->returnValue(false));
        $appServerDao->expects($this->once())->method('insert')->will($this->returnValue($id));

        $server = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);
        $server->setId('testId');
        $server->setTimestamp('timestamp');
        $server->setIp('127.0.0.1');
        $server->setLastFrontendUsage('frontendUsageTimestamp');
        $server->setLastAdminUsage('adminUsageTimestamp');
        $server->setIsValid();

        $service = $this->getApplicationServerServiceMock($appServerDao);

        $this->assertEquals($id, $service->save($server));
    }

    private function getApplicationServerServiceMock($appServerDao)
    {
        $config = Registry::getConfig();
        $service = $this->getMock(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            array("getAppServerDao"),
            array($config));
        $service->expects($this->any())->method('getAppServerDao')->will($this->returnValue($appServerDao));

        return $service;
    }

    private function getApplicationServerDaoMock($methodToMock, $expectedReturnValue)
    {
        $databaseProvider = oxNew(DatabaseProvider::class);
        $config = Registry::getConfig();
        $appServerDao = $this->getMock(
            \OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class,
            array($methodToMock),
            array($databaseProvider, $config));
        $appServerDao->expects($this->once())->method($methodToMock)->will($this->returnValue($expectedReturnValue));

        return $appServerDao;
    }
}
