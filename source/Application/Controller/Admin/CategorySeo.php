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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;

/**
 * Category seo config class
 */
class CategorySeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{

    /**
     * Updating showsuffix field
     *
     * @return null
     */
    public function save()
    {
        $sOxid = $this->getEditObjectId();
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($sOxid)) {
            $blShowSuffixParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('blShowSuffix');
            $sShowSuffixField = 'oxcategories__oxshowsuffix';
            $oCategory->$sShowSuffixField = new \OxidEsales\Eshop\Core\Field((int) $blShowSuffixParameter);
            $oCategory->save();

            $this->_getEncoder()->markRelatedAsExpired($oCategory);
        }

        return parent::save();
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return oxSeoEncoderCategory
     */
    protected function _getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class);
    }

    /**
     * This SEO object supports suffixes so return TRUE
     *
     * @return bool
     */
    public function isSuffixSupported()
    {
        return true;
    }

    /**
     * Returns url type
     *
     * @return string
     */
    protected function _getType()
    {
        return 'oxcategory';
    }

    /**
     * Returns true if SEO object id has suffix enabled
     *
     * @return bool
     */
    public function isEntrySuffixed()
    {
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($this->getEditObjectId())) {
            return (bool) $oCategory->oxcategories__oxshowsuffix->value;
        }
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($oCategory->load($this->getEditObjectId())) {
            return $this->_getEncoder()->getCategoryUri($oCategory, $this->getEditLang());
        }
    }
}
