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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Autoload;

/**
 * This class autoloads backwards compatible classes by triggering the composer autoloader via a unified namespace
 * class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class BackwardsCompatibilityAutoload
{
    /**
     * Array map with Unified Namespace class name as key, bc class name as value.
     * @var array
     */
    private $backwardsCompatibilityClassMap = null;

    /**
     * Autoload method.
     *
     * @param string $class Name of the class to be loaded
     */
    public function autoload($class)
    {
        $unifiedNamespaceClassName = $this->getUnifiedNamespaceClassForBcAlias($class);
        if (!empty($unifiedNamespaceClassName)) {
            $this->forceBackwardsCompatiblityClassLoading($unifiedNamespaceClassName);
        }
    }

    /**
     * Return the name of a Unified Namespace class for a given backwards compatible class
     *
     * @param string $bcAlias Name of the backwards compatible class like oxArticle
     *
     * @return string Name of the unified namespace class like OxidEsales\Eshop\Application\Model\Article
     */
    private function getUnifiedNamespaceClassForBcAlias($bcAlias)
    {
        $classMap = $this->getBackwardsCompatibilityClassMap();
        $bcAlias = strtolower($bcAlias);
        $result = isset($classMap[$bcAlias]) ? $classMap[$bcAlias] : "";
        return $result;
    }

    /**
     * This triggers loading the unified namespace class via composer autoloader and also the
     * aliasing of the backwards compatible class.
     *
     * @param string $class Name of the class to load
     */
    private function forceBackwardsCompatiblityClassLoading($class)
    {
        class_exists($class);
    }

    /**
     * Return the backwards compatible class map.
     *
     * @return array Mapping of Unified Namespace to backwards compatible classes.
     */
    private function getBackwardsCompatibilityClassMap()
    {
        if (is_null($this->backwardsCompatibilityClassMap)) {
            $classMap = include __DIR__ . DIRECTORY_SEPARATOR . 'BackwardsCompatibilityClassMap.php';
            $this->backwardsCompatibilityClassMap = $classMap;
        }

        return $this->backwardsCompatibilityClassMap;
    }
}
spl_autoload_register([new BackwardsCompatibilityAutoload(), 'autoload']);