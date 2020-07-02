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
 * Presets management
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_clboost\output;

use action_menu;
use action_menu_filler;
use action_menu_link_secondary;
use html_writer;
use pix_icon;
use stdClass;
use context_course;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    use override_trait;
    /**
     * Get Logo URL
     * If it has not been overriden by core_admin config, serve the logo in pix
     */
    /**
     * Get Logo URL
     * If it has not been overriden by core_admin config, serve the logo in pix
     *
     * @param null $maxwidth
     * @param int $maxheight
     * @return bool|false|\moodle_url
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        $logourl = parent::get_logo_url($maxwidth, $maxheight);
        if (!$logourl) {
            global $CFG;
            $logourl =new \moodle_url('/theme/clboost/pix/logo.png');
        }
        return $logourl;
    }

    /**
     * Get the compact logo URL.
     *
     * @return string
     */
    /**
     * Get the compact logo URL.
     *
     * @param int $maxwidth
     * @param int $maxheight
     * @return bool|false|\moodle_url
     */
    public function get_compact_logo_url($maxwidth = 100, $maxheight = 100) {
        $compactlogourl = parent::get_compact_logo_url($maxwidth, $maxheight);
        if (!$compactlogourl) {
            global $CFG;
            $compactlogourl = new \moodle_url('/theme/clboost/pix/logo-compact.png');
        }
        return $compactlogourl;
    }

    public function should_display_navbar_logo() {
        $logo = $this->get_compact_logo_url();
        return !empty($logo);
    }

    public function extra_preset_footer() {
        $pinstance = \theme_clboost\presets\presets_base::get_current_preset_instance();
        if ($pinstance) {
            return $pinstance->get_extra_footer_content();
        }
        return '';
    }

    /**
     * Check if it is on frontpage or not
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
        global $CFG;

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
        $pinstance = \theme_clboost\presets\presets_base::get_current_preset_instance();
        if ($pinstance && !$this->is_on_frontpage()) {
            $context = $pinstance->get_extra_context();
            $headerimages = isset($context['headerimages']) ? $context['headerimages'] : null;
            if ($headerimages) {
                $randomimage = count($headerimages) > 1 ? rand(0, count($headerimages) - 1) : 0;
                $header->headerimg = $headerimages[$randomimage];
            }
        }
        return $this->render_from_template('core/full_header', $header);
    }

}
