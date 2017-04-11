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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */

require_once 'bootstrap.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Class generateRealVirtualClasses
 */
class RealVirtualClassesGenerator
{

    /**
     *
     */
    public function generate()
    {
        $virtualNamespaceProvider = new \OxidEsales\Eshop\Core\Autoload\VirtualNameSpaceClassMapProvider();
        $classMap = $virtualNamespaceProvider->getClassMap();
        foreach ($classMap as $virtualClass => $realClass) {
            echo $virtualClass . PHP_EOL;
            $virtualClassPath = OX_BASE_PATH .
                                'Core' . DIRECTORY_SEPARATOR .
                                'Autoload' . DIRECTORY_SEPARATOR .
                                'VirtualNameSpace' . DIRECTORY_SEPARATOR .
                                str_replace('\\', DIRECTORY_SEPARATOR, $virtualClass) .
                                '.php';
            if (!is_dir(dirname($virtualClassPath))) {
                if (!mkdir(dirname($virtualClassPath), 0777, true)) {
                    $message = 'Error: could not create directory ' . dirname($virtualClassPath) . PHP_EOL;
                    die($message);
                }
            }

            $parts = explode('\\', $virtualClass);
            $className = array_pop($parts);
            $parentClass = '\\'. $realClass;
            $objectType = strpos($className, 'Interface') ? 'interface' : 'class';
            $namespace = implode('\\', $parts);
            if (!file_exists($virtualClassPath)) {
                if (!file_put_contents(
                    $virtualClassPath,
                    "<?php
namespace $namespace;

/**
 * $objectType $className
 */
$objectType $className extends $parentClass
{

}
"
                )
                ) {
                    $message = 'Error: could not create file ' . $virtualClassPath . PHP_EOL;
                    die($message);
                }
            }
        }
    }
}


$realVirtualClassesGenerator = new RealVirtualClassesGenerator();
$realVirtualClassesGenerator->generate();
