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
use oxAdminView;

/**
 * Admin article main voucherserie manager.
 * There is possibility to change voucherserie name, description, valid terms
 * and etc.
 * Admin Menu: Shop Settings -> Vouchers -> Main.
 */
class VoucherSerieMain extends \OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController
{

    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = "voucherSerie_generate";

    /**
     * Voucher serie object
     *
     * @var oxvoucherserie
     */
    protected $_oVoucherSerie = null;

    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = "voucherserie_main.tpl";

    /**
     * View id, use old class name for compatibility reasons.
     *
     * @var string
     */
    protected $viewId = 'voucherserie_main';

    /**
     * Executes parent method parent::render(), creates oxvoucherserie object,
     * passes data to Smarty engine and returns name of template file
     * "voucherserie_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oVoucherSerie = oxNew(\OxidEsales\Eshop\Application\Model\VoucherSerie::class);
            $oVoucherSerie->load($soxId);
            $this->_aViewData["edit"] = $oVoucherSerie;

            //Disable editing for derived items
            if ($oVoucherSerie->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        return $this->_sThisTemplate;
    }

    /**
     * Saves main Voucherserie parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        // Parameter Processing
        $soxId = $this->getEditObjectId();
        $aSerieParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        // Voucher Serie Processing
        $oVoucherSerie = oxNew(\OxidEsales\Eshop\Application\Model\VoucherSerie::class);
        // if serie already exist use it
        if ($soxId != "-1") {
            $oVoucherSerie->load($soxId);
        } else {
            $aSerieParams["oxvoucherseries__oxid"] = null;
        }

        //Disable editing for derived items
        if ($oVoucherSerie->isDerived()) {
            return;
        }

        $aSerieParams["oxvoucherseries__oxdiscount"] = abs($aSerieParams["oxvoucherseries__oxdiscount"]);

        $oVoucherSerie->assign($aSerieParams);
        $oVoucherSerie->save();

        // set oxid if inserted
        $this->setEditObjectId($oVoucherSerie->getId());
    }

    /**
     * Returns voucher status information array
     *
     * @return array
     */
    public function getStatus()
    {
        if ($oSerie = $this->_getVoucherSerie()) {
            return $oSerie->countVouchers();
        }
    }

    /**
     * Overriding parent function, doing nothing..
     */
    public function prepareExport()
    {
    }


    /**
     * Returns voucher serie object
     *
     * @return oxvoucherserie
     */
    protected function _getVoucherSerie()
    {
        if ($this->_oVoucherSerie == null) {
            $oVoucherSerie = oxNew(\OxidEsales\Eshop\Application\Model\VoucherSerie::class);
            $sId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("voucherid");
            if ($oVoucherSerie->load($sId ? $sId : \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("voucherid"))) {
                $this->_oVoucherSerie = $oVoucherSerie;
            }
        }

        return $this->_oVoucherSerie;
    }

    /**
     * Prepares Export
     *
     * @return null
     */
    public function start()
    {
        $sVoucherNr = trim(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("voucherNr"));
        $bRandomNr = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("randomVoucherNr");
        $controllerId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestControllerId();

        if ($controllerId == 'voucherserie_generate' && !$bRandomNr && empty($sVoucherNr)) {
            return;
        }

        $this->_aViewData['refresh'] = 0;
        $this->_aViewData['iStart'] = 0;
        $iEnd = $this->prepareExport();
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iEnd", $iEnd);
        $this->_aViewData['iEnd'] = $iEnd;

        // saving export info
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("voucherid", \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("voucherid"));
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("voucherAmount", abs((int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("voucherAmount")));
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("randomVoucherNr", $bRandomNr);
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("voucherNr", $sVoucherNr);
    }

    /**
     * Current view ID getter helps to identify navigation position
     * fix for 0003701, passing dynexportbase::getViewId
     *
     * @return string
     */
    public function getViewId()
    {
        return \OxidEsales\Eshop\Application\Controller\Admin\AdminController::getViewId();
    }
}
