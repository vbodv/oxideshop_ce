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

namespace OxidEsales\EshopCommunity\Core\Service;

/**
 * Prepare application servers information for export.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ApplicationServerFacade
{
    /**
     * @var \OxidEsales\Eshop\Core\Service\ApplicationServerService
     */
    private $appServerService;

    /**
     * ApplicationServerFacade constructor.
     */
    public function __construct()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $this->appServerService = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class, $config);
    }

    /**
     * Return active server nodes.
     *
     * @return array
     */
    public function getApplicationServerList()
    {
        $activeServerCollection = [];

        $activeServers = (array) $this->getApplicationServerService()->loadActiveAppServerList();
        foreach ($activeServers as $server) {
            if ($this->validateServerListItem($server)) {
                $activeServerCollection[] = array(
                    'id' => $server->getId(),
                    'ip' => $server->getIp(),
                    'lastFrontendUsage' => $server->getLastFrontendUsage(),
                    'lastAdminUsage' => $server->getLastAdminUsage()
                );
            }
        }

        return $activeServerCollection;
    }

    /**
     * Return application server service object.
     *
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerService
     */
    protected function getApplicationServerService()
    {
        return $this->appServerService;
    }

    /**
     * Checks if object is an instance of \OxidEsales\Eshop\Core\ApplicationServer.
     *
     * @param object $server Object to check
     *
     * @return bool
     */
    private function validateServerListItem($server)
    {
        return ($server instanceof \OxidEsales\Eshop\Core\ApplicationServer);
    }
}
