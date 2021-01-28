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
 * Contain the logic for a drawer.
 *
 * This is very similar to the boost routine (drawer.js), except that we have two states:
 * - Partially open with just icons showing up
 * - Fully opened
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'theme_boost/drawer'],
    function ($, BoostDrawer) {
        /* This is a copy of the drawer.js variables as they are private */
        var SELECTORS = {
            TOGGLE_REGION: '[data-region="drawer-toggle"]',
            TOGGLE_ACTION: '[data-action="toggle-drawer"]',
            TOGGLE_TARGET: 'aria-controls',
            TOGGLE_SIDE: 'left',
            BODY: 'body',
            SECTION: '.list-group-item[href*="#section-"]',
            DRAWER: '#nav-drawer'
        };
        return {
            'init': function () {
                var maximisebutton = $(SELECTORS.DRAWER + ' .nav-drawer-maximise-action');

                if (maximisebutton) {
                    maximisebutton.click(function (e) {
                        $(SELECTORS.TOGGLE_ACTION).triggerHandler("click");
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                    });
                }
                $(SELECTORS.TOGGLE_REGION).each(function(index, ele) {
                    var trigger = $(ele).find(SELECTORS.TOGGLE_ACTION);
                    var side = trigger.attr('data-side');
                    var body = $(SELECTORS.BODY);
                    // We add a class drawer-right or drawer-left so this can be used to determine
                    // Left or right margin in scss.
                    body.addClass('drawer-' + side);
                });
                return BoostDrawer.init();
            }
        };
    });
