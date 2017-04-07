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

use \ArrayIterator;
use \ArrayAccess;

/**
 * A ApplicationServerList represents a set of ApplicationServer instances.
 *
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class ApplicationServerList implements ArrayAccess
{
    /**
     * @var ApplicationServer[]
     */
    private $applicationServers = array();

    /**
     * Adds an ApplicationServer.
     *
     * @param string            $id      An ApplicationServer id
     * @param ApplicationServer $servers An ApplicationServer instance
     */
    public function add($id, $servers)
    {
        $this->applicationServers[$id] = $servers;
    }

    /**
     * Return the list of ApplicationServer instances.
     *
     * @return ApplicationServer[] An array of ApplicationServer instance
     */
    public function getApplicationServers()
    {
        return $this->applicationServers;
    }

    /**
     * Whether an application server exists.
     *
     * @param string $offset A Server Id to check for.
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->applicationServers[$offset]);
    }

    /**
     * An application server to retrieve.
     *
     * @param string $offset A Server Id to retrieve.
     *
     * @return ApplicationServer An ApplicationServer instance.
     */
    public function offsetGet($offset)
    {
        $server = null;
        if ($this->offsetExists($offset)) {
            $server = $this->applicationServers[$offset];
        }
        return $server;
    }

    /**
     * An application server to set
     *
     * @param string $offset A Server Id to assign the value to.
     *
     * @param ApplicationServer $value An ApplicationServer instance.
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->applicationServers[] = $value;
        } else {
            $this->applicationServers[$offset] = $value;
        }
    }

    /**
     * An application server to unset
     *
     * @param string $offset A Server Id to unset.
     */
    public function offsetUnset($offset)
    {
        unset($this->applicationServers[$offset]);
    }
}
