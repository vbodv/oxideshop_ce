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

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;
use oxDb;
use oxUtilsObject;

/**
 * Manages application servers information.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ServersManager
{

    /**
     * Time in seconds, server node information life time.
     */
    const NODE_AVAILABILITY_CHECK_PERIOD = 86400;

    /**
     * Time in seconds, server node information life time.
     */
    const INACTIVE_NODE_STORAGE_PERIOD = 259200;

    /**
     * The name of config option for saving servers data information.
     */
    const CONFIG_NAME_FOR_SERVER_INFO = 'aServersData_';

    /**
     * Returns server based on server id.
     *
     * @param string $sServerId
     *
     * @return \OxidEsales\Eshop\Core\ApplicationServer
     */
    public function getServer($sServerId)
    {
        $aServerData = $this->getServerDataFromDb($sServerId);

        return $this->createServer($sServerId, $aServerData);
    }

    /**
     * Saves given server information to config.
     *
     * @param \OxidEsales\Eshop\Core\ApplicationServer $oServer
     */
    public function saveServer($oServer)
    {
        $aServerData = array(
            'id'                => $oServer->getId(),
            'timestamp'         => $oServer->getTimestamp(),
            'ip'                => $oServer->getIp(),
            'lastFrontendUsage' => $oServer->getLastFrontendUsage(),
            'lastAdminUsage'    => $oServer->getLastAdminUsage(),
            'isValid'           => $oServer->isValid()
        );
        $this->saveToDb($oServer->getId(), $aServerData);
    }

    /**
     * Creates ApplicationServer from given server id and data.
     *
     * @param string $sServerId
     * @param array  $aData
     *
     * @return \OxidEsales\Eshop\Core\ApplicationServer
     */
    protected function createServer($sServerId, $aData = array())
    {
        /** @var \OxidEsales\Eshop\Core\ApplicationServer $oAppServer */
        $oAppServer = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);

        $oAppServer->setId($sServerId);
        $oAppServer->setTimestamp($this->getServerParameter($aData, 'timestamp'));
        $oAppServer->setIp($this->getServerParameter($aData, 'ip'));
        $oAppServer->setLastFrontendUsage($this->getServerParameter($aData, 'lastFrontendUsage'));
        $oAppServer->setLastAdminUsage($this->getServerParameter($aData, 'lastAdminUsage'));
        $oAppServer->setIsValid($this->getServerParameter($aData, 'isValid'));

        return $oAppServer;
    }

    /**
     * Gets server parameter.
     *
     * @param array  $aData Data
     * @param string $sName Name
     *
     * @return mixed
     */
    protected function getServerParameter($aData, $sName)
    {
        return array_key_exists($sName, $aData) ? $aData[$sName] : null;
    }

    /**
     * Return active server nodes.
     *
     * @return array
     */
    public function getServers()
    {
        $appServerList = $this->getServersData();
        $appServerList = $this->markInActiveServers($appServerList);
        $appServerList = $this->deleteInActiveServers($appServerList);

        $aValidServers = array();
        /** @var ApplicationServer $oServer */
        foreach ($appServerList->getApplicationServers() as $oServer) {
            if ($oServer->isValid()) {
                $aValidServers[] = array(
                    'id'                => $oServer->getId(),
                    'ip'                => $oServer->getIp(),
                    'lastFrontendUsage' => $oServer->getLastFrontendUsage(),
                    'lastAdminUsage'    => $oServer->getLastAdminUsage()
                );
            }
        }

        return $aValidServers;
    }

    /**
     * Removes server node information.
     *
     * @param string $sServerId Server id
     */
    public function deleteServer($sServerId)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sShopId = $oConfig->getBaseShopId();
        $sVarName = self::CONFIG_NAME_FOR_SERVER_INFO.$sServerId;
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "DELETE FROM oxconfig WHERE oxvarname = ? and oxshopid = ?";
        $oDb->execute($sQ, array($sVarName, $sShopId));
    }

    /**
     * Mark servers as inactive if they are not used anymore.
     *
     * @param ApplicationServerList $appServerList Information of all servers data
     *
     * @return ApplicationServerList $appServerList Information of all servers data
     */
    public function markInActiveServers($appServerList)
    {
        /** @var ApplicationServer $oServer */
        foreach ($appServerList->getApplicationServers() as $oServer) {
            if ($this->needToCheckApplicationServerAvailability($oServer->getTimestamp())) {
                $oServer->setIsValid(false);
                $this->saveServer($oServer);
                $appServerList->offsetSet($oServer->getId(), $oServer);
            }
        }
        return $appServerList;
    }

    /**
     * Check if application server availability check period is over.
     *
     * @param int $timestamp A timestamp when last time server was checked.
     *
     * @return bool
     */
    protected function needToCheckApplicationServerAvailability($timestamp)
    {
        return (bool) ($timestamp < \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime() - self::NODE_AVAILABILITY_CHECK_PERIOD);
    }

    /**
     * Removes information about old and not used servers.
     *
     * @param ApplicationServerList $appServerList Information of all servers data
     *
     * @return ApplicationServerList $appServerList Information of all servers data
     */
    public function deleteInActiveServers($appServerList)
    {
        /** @var ApplicationServer $oServer */
        foreach ($appServerList->getApplicationServers() as $oServer) {
            if ($this->needToDeleteInactiveApplicationServer($oServer->getTimestamp())) {
                $this->deleteServer($oServer->getId());
                $appServerList->offsetUnset($oServer->getId());
            }
        }
        return $appServerList;
    }

    /**
     * Check if application server availability check period is over.
     *
     * @param int $timestamp A timestamp when last time server was checked.
     *
     * @return bool
     */
    protected function needToDeleteInactiveApplicationServer($timestamp)
    {
        return (bool) ($timestamp < \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime() - self::INACTIVE_NODE_STORAGE_PERIOD);
    }

    /**
     * Returns all servers information array from configuration.
     *
     * @return ApplicationServerList
     */
    public function getServersData()
    {
        $appServerList = oxNew(\OxidEsales\Eshop\Core\ApplicationServerList::class);

        $resultFromDatabase = $this->getAllServersDataConfigsFromDb();
        if ($resultFromDatabase != false && $resultFromDatabase->count() > 0) {
            while (!$resultFromDatabase->EOF) {
                $sServerId = $this->parseServerIdFromConfig($resultFromDatabase->fields['oxvarname']);
                $appServerList->add($sServerId, $this->createServer($sServerId, (array)unserialize($resultFromDatabase->fields['oxvarvalue'])));
                $resultFromDatabase->fetchRow();
            }
        }
        return $appServerList;
    }

    /**
     * Parses config option name to get the server id.
     *
     * @param string $sVarName The name of the config option.
     *
     * @return string The id of server.
     */
    private function parseServerIdFromConfig($sVarName)
    {
        $iConstNameLength = strlen(self::CONFIG_NAME_FOR_SERVER_INFO);
        $sId = substr($sVarName, $iConstNameLength);
        return $sId;
    }

    /**
     * Returns all servers information array from database.
     *
     * @return object ResultSetInterface
     */
    protected function getAllServersDataConfigsFromDb()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $sConfigsQuery = "SELECT oxvarname, " . $oConfig->getDecodeValueQuery() .
            " as oxvarvalue FROM oxconfig WHERE oxvarname like ? AND oxshopid = ?";

        return $database->select($sConfigsQuery, array(self::CONFIG_NAME_FOR_SERVER_INFO."%", $oConfig->getBaseShopId()));
    }

    /**
     * Returns server information from configuration.
     *
     * @param string $sServerId
     *
     * @return array
     */
    protected function getServerDataFromDb($sServerId)
    {
        $aServerData = array();
        $sData = $this->getConfigValueFromDB(self::CONFIG_NAME_FOR_SERVER_INFO.$sServerId);

        if ($sData != false ) {
            $aServerData = (array)unserialize($sData);
        }
        return $aServerData;
    }

    /**
     * Returns configuration value from database.
     *
     * @param string $sVarName Variable name
     *
     * @return string
     */
    private function getConfigValueFromDB($sVarName)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sConfigsQuery = "SELECT " . $oConfig->getDecodeValueQuery() .
            " as oxvarvalue FROM oxconfig WHERE oxvarname = ? AND oxshopid = ?";

        $sResult = $oDb->getOne($sConfigsQuery, array($sVarName, $oConfig->getBaseShopId()), false);

        return $sResult;
    }

    /**
     * Saves servers data to database.
     *
     * @param string $sServerId Server id
     * @param array $aServerData Server data
     */
    protected function saveToDb($sServerId, $aServerData)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sVarName = self::CONFIG_NAME_FOR_SERVER_INFO.$sServerId;
        $sConfigKey = $oConfig->getConfigParam('sConfigKey');
        $sValue = serialize($aServerData);
        $sVarType = 'arr';
        $sShopId = $oConfig->getBaseShopId();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        if ($this->getConfigValueFromDB($sVarName) !== false) {
            $sQ = "UPDATE oxconfig SET oxvarvalue=ENCODE( ?, ?) WHERE oxvarname = ? and oxshopid = ?";
            $oDb->execute($sQ, array($sValue, $sConfigKey, $sVarName, $sShopId));
        } else {
            $sOxid = \OxidEsales\Eshop\Core\UtilsObject::getInstance()->generateUID();

            $sQ = "insert into oxconfig (oxid, oxshopid, oxmodule, oxvarname, oxvartype, oxvarvalue)
               values(?, ?, '', ?, ?, ENCODE( ?, ?) )";
            $oDb->execute($sQ, array($sOxid, $sShopId, $sVarName, $sVarType, $sValue, $sConfigKey));
        }
    }
}
