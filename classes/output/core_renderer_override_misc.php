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
 * Core renderer functionalities
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_clboost\output;

use html_writer;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
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
     */
    public function standard_head_html() {
        $output = parent::standard_head_html();
        $currentthemename = $this->page->theme->name;
        $gacode = get_config('theme_' . $currentthemename, 'ganalytics');
        if ($gacode) {
            $output .= \html_writer::tag('script', '', array(
                'src' => "https://www.googletagmanager.com/gtag/js?id={$gacode}",
                'async' => ''
            ));
            $output .= \html_writer::script("
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                        gtag('js', new Date());
                        gtag('config', '{$gacode}');
                    "
            );
        }
        return $output;
    }

}