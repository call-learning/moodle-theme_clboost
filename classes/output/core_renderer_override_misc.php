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
namespace theme_clboost\output;

use html_writer;
use stdClass;


/**
 * Core renderer functionalities
 *
 * This trait is hopefully temporary. Here we override functions from core renderer
 * that would need to be broken down into more manageable (and configurable pieces)
 *
 * @package theme_clboost
 */
trait core_renderer_override_misc {
    /**
     * Check if it is on frontpage or not
     *
     * @return bool
     */
    public function is_on_frontpage() {
        return ($this->page->pagelayout == 'frontpage');
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        if ($this->page->include_region_main_settings_in_header_actions() && !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        $header->headerimg = '';
        return $this->render_from_template('core/full_header', $header);
    }

    /**
     * Add google analytics code
     * Do not add analytics for admin user
     */
    public function standard_head_html() {
        $output = parent::standard_head_html();
        $currentthemename = $this->page->theme->name;
        $gacode = get_config('theme_' . $currentthemename, 'ganalytics');
        $gatriggerid = get_config('theme_' . $currentthemename, 'ganalyticstrigger');
        if ($gacode && !is_siteadmin() && $gatriggerid) {
            $output .= html_writer::script("
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                 })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
                "
            );
            $this->page->requires->js_amd_inline("$(document).on('grpd_policies_accepted',
                    function(event, policies) {
                    if (policies) {
                        const found = policies.find((policy) => { return policy.id === {$gatriggerid}; }
                        if (found) {
                            ga('create', '{$gacode}', 'auto');
                            ga('set', 'anonymizeIp', true);
                            ga('set', 'allowAdFeatures', false);
                            ga('send', 'pageview');
                        }
                    }
                 );");

        }
        return $output;
    }
}
