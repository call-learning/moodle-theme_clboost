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
 * This is the main analytics code that will trigger google analytics when
 * the right event is triggered (policy accepted)
 *
 * @module   theme_clboost
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Templates from 'core/templates';
import log from 'core/log';
/**
 * Initialize the analytics code
 * @param {String} gatriggerid
 * @param {String} gacode
 */
export const init = (gatriggerid, gacode) => {
    const checkGAPolicyAccepted = (event) => {
        const policies = event.detail;
        if (policies) {
            const found = policies.find(
                (policy) => {
                    return policy.policyversionid === gatriggerid;
                }
            );
            if (found && found.accepted) {
                Templates.render('theme_clboost/gtag', {gtagtrackingid: gacode}).then((html, js) => {
                    const ga = document.createElement('div');
                    document.body.appendChild(ga);
                    Templates.replaceNodeContents(ga, html, js);
                    return true;
                }).catch((error) => {
                    log.error(error);
                });
            }
        }
    };
    document.addEventListener('grpd_policies_accepted', checkGAPolicyAccepted);
    document.addEventListener('grpd_policies_current_status', checkGAPolicyAccepted);
};