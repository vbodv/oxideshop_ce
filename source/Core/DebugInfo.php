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

namespace OxidEsales\Eshop\Core;

use oxRegistry;

/**
 * Debug information formatter
 *
 */
class DebugInfo
{

    /**
     * format template data for debug view
     *
     * @param array $aViewData template data
     *
     * @return string
     */
    public function formatTemplateData($aViewData = array())
    {
        $sLog = '';
        reset($aViewData);
        while (list($sViewName, $oViewData) = each($aViewData)) {
            // show debbuging information
            $sLog .= "TemplateData[$sViewName] : <br />\n";
            $sLog .= print_r($oViewData, 1);
        }

        return $sLog;
    }

    /**
     * format memory usage
     *
     * @return string
     */
    public function formatMemoryUsage()
    {
        $sLog = '';
        if (function_exists('memory_get_usage')) {
            $iKb = ( int ) (memory_get_usage() / 1024);
            $iMb = round($iKb / 1024, 3);
            $sLog .= 'Memory usage: ' . $iMb . ' MB';

            if (function_exists('memory_get_peak_usage')) {
                $iPeakKb = ( int ) (memory_get_peak_usage() / 1024);
                $iPeakMb = round($iPeakKb / 1024, 3);
                $sLog .= ' (peak: ' . $iPeakMb . ' MB)';
            }
            $sLog .= '<br />';

            if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
                $iKb = ( int ) (memory_get_usage(true) / 1024);
                $iMb = round($iKb / 1024, 3);
                $sLog .= 'System memory usage: ' . $iMb . ' MB';

                if (function_exists('memory_get_peak_usage')) {
                    $iPeakKb = ( int ) (memory_get_peak_usage(true) / 1024);
                    $iPeakMb = round($iPeakKb / 1024, 3);
                    $sLog .= ' (peak: ' . $iPeakMb . ' MB)';
                }
                $sLog .= '<br />';
            }
        }

        return $sLog;
    }

    /**
     * format execution times
     *
     * @param double $dTotalTime total time
     *
     * @return string
     */
    public function formatExecutionTime($dTotalTime)
    {
        $sLog = 'Execution time:' . round($dTotalTime, 4) . '<br />';
        global $aProfileTimes;
        global $aExecutionCounts;
        if (is_array($aProfileTimes)) {
            $sLog .= "----------------------------------------------------------<br>" . PHP_EOL;
            arsort($aProfileTimes);
            $sLog .= "<table cellspacing='10px' style='border: 1px solid #000'>";
            foreach ($aProfileTimes as $sKey => $sVal) {
                $sLog .= "<tr><td style='border-bottom: 1px dotted #000;min-width:300px;'>Profile $sKey: </td><td style='border-bottom: 1px dotted #000;min-width:100px;'>" . round($sVal, 5) . "s</td>";
                if ($dTotalTime) {
                    $sLog .= "<td style='border-bottom: 1px dotted #000;min-width:100px;'>" . round($sVal * 100 / $dTotalTime, 2) . "%</td>";
                }
                if ($aExecutionCounts[$sKey]) {
                    $sLog .= " <td style='border-bottom: 1px dotted #000;min-width:50px;padding-right:30px;' align='right'>" . $aExecutionCounts[$sKey] . "</td>"
                             . "<td style='border-bottom: 1px dotted #000;min-width:15px; '>*</td>"
                             . "<td style='border-bottom: 1px dotted #000;min-width:100px;'>" . round($sVal / $aExecutionCounts[$sKey], 5) . "s</td>" . PHP_EOL;
                } else {
                    $sLog .= " <td colspan=3 style='border-bottom: 1px dotted #000;min-width:100px;'> not stopped correctly! </td>" . PHP_EOL;
                }
                $sLog .= '</tr>';
            }
            $sLog .= "</table>";
        }

        return $sLog;
    }

    /**
     * general info (debug title)
     *
     * @return string
     */
    public function formatGeneralInfo()
    {
        $sLog = "cl=" . oxRegistry::getConfig()->getActiveView()->getClassName();
        if (($sFnc = oxRegistry::getConfig()->getActiveView()->getFncName())) {
            $sLog .= " fnc=$sFnc";
        }

        return $sLog;
    }

    /**
     * Forms view name and timestamp to.
     *
     * @return string
     */
    public function formatTimeStamp()
    {
        $sLog = '';
        $sClassName = oxRegistry::getConfig()->getActiveView()->getClassName();
        $sLog .= "<div id='" . $sClassName . "_executed'>Executed: " . date('Y-m-d H:i:s') . "</div>";
        $sLog .= "<div id='" . $sClassName . "_timestamp'>Timestamp: " . microtime(true) . "</div>";

        return $sLog;
    }
}
