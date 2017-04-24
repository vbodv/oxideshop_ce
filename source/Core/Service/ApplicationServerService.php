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
 * Manages application servers information.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ApplicationServerService implements \OxidEsales\Eshop\Core\Contract\ApplicationServerServiceInterface
{
    /**
     * @var \OxidEsales\Eshop\Core\Dao\ApplicationServerDao The Dao object for application server.
     */
    private $appServerDao;

    /**
     * ApplicationServerService constructor.
     *
     * @param \OxidEsales\Eshop\Core\Config $config Main shop configuration class.
     */
    public function __construct($config)
    {
        $databaseProvider = oxNew(\OxidEsales\Eshop\Core\DatabaseProvider::class);

        $this->appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
    }

    /**
     * Returns all servers information array from configuration.
     *
     * @return array
     */
    public function loadList()
    {
        return $this->getAppServerDao()->findAll();
    }

    /**
     * Load the application server for given id.
     *
     * @param string $id The id of the application server to load.
     *
     * @return \OxidEsales\Eshop\Core\ApplicationServer
     */
    public function load($id)
    {
        return $this->getAppServerDao()->findById($id);
    }

    /**
     * Removes server node information.
     *
     * @param string $serverId
     */
    public function deleteById($serverId)
    {
        return $this->getAppServerDao()->delete($serverId);
    }

    /**
     * Saves application server data.
     *
     * @param \OxidEsales\Eshop\Core\ApplicationServer $appServer
     *
     * @return int
     */
    public function save($appServer)
    {
        if ($this->getAppServerDao()->findById($appServer->getId()) !== false) {
            $effectedRows = $this->getAppServerDao()->update($appServer);
        } else {
            $effectedRows = $this->getAppServerDao()->insert($appServer);
        }
        return $effectedRows;
    }

    /**
     * Returns ApplicationServerDao class.
     *
     * @return \OxidEsales\Eshop\Core\Dao\ApplicationServerDao
     */
    public function getAppServerDao()
    {
        return $this->appServerDao;
    }
}
