<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for theme_clboost\local\utils
 *
 * @package   theme_clboost
 * @category   test
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_clboost\local\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for theme_clboost\local\utils
 *
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_clboost_utils_test extends advanced_testcase {

    /**
     * Test real theme path
     *
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function test_get_real_theme_path() {
        global $CFG;
        $theme = theme_config::load('clboost');
        $this->assertEquals($CFG->dirroot . '/theme/clboost/', utils::get_real_theme_path($theme));
        $this->assertEquals($CFG->dirroot . '/theme/clboost/layout/frontpage.php',
            utils::get_real_theme_path($theme, 'layout/frontpage.php'));
    }

    /**
     * Convert from config
     */
    public function test_convert_from_config() {
        $lineparser = function ($setting, $index, &$currentobject) {
            if (!empty($setting)) {
                switch ($index) {
                    case 0:
                        $currentobject->title = $setting;
                        break;
                    case 1:
                        $currentobject->address = $setting;
                        break;
                    case 2:
                        $currentobject->tel = $setting;
                        break;
                }
            }
        };
        $testconfig = "AddressName1|PostalAddress1|Tel1\nAddressName2|PostalAddress2|Tel2\n";
        $testconfig2 = "AddressName1;PostalAddress1;Tel1\nAddressName2;PostalAddress2;Tel2\n";

        $parsed1 = utils::convert_from_config($testconfig, $lineparser);
        $parsed2 = utils::convert_from_config($testconfig2, $lineparser, ';');
        $expectedresults =
            json_decode(
                '[{"title":"AddressName1","address":"PostalAddress1","tel":"Tel1"},'
                .'{"title":"AddressName2","address":"PostalAddress2","tel":"Tel2"}]');
        $this->assertEquals($expectedresults , $parsed1);
        $this->assertEquals($expectedresults, $parsed2);
    }

    /**
     * Convert from config with empties
     */
    public function test_convert_from_config_with_empties() {
        $lineparser = function ($setting, $index, &$currentobject) {
            if (!empty($setting)) {
                switch ($index) {
                    case 0:
                        $currentobject->title = $setting;
                        break;
                    case 1:
                        $currentobject->address = $setting;
                        break;
                    case 2:
                        $currentobject->postcode = $setting;
                        break;
                    case 3:
                        $currentobject->tel = $setting;
                        break;
                }
            }
        };
        $testconfig = "AddressName1|PostalAddress1||Tel1\nAddressName2|PostalAddress2|Postcode|Tel2\n";
        $testconfig2 = "AddressName1;PostalAddress1;;Tel1\nAddressName2;PostalAddress2;Postcode;Tel2\n";

        $parsed1 = utils::convert_from_config($testconfig, $lineparser);
        $parsed2 = utils::convert_from_config($testconfig2, $lineparser, ';');
        $expectedresults =
            json_decode(
                '[{"title":"AddressName1","address":"PostalAddress1","tel":"Tel1"},'
                .'{"title":"AddressName2","address":"PostalAddress2","postcode":"Postcode","tel":"Tel2"}]');
        $this->assertEquals($expectedresults , $parsed1);
        $this->assertEquals($expectedresults, $parsed2);
    }
}
