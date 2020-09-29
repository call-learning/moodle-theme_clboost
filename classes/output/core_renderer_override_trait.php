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
 * This trait is hopefully temporary. Here we override functions from core renderer
 * that would need to be broken down into more manageable (and configurable pieces)
 *
 * @package theme_clboost\output
 */
trait core_renderer_override_trait {
    /**
     * This is a integral copy of the core_renderer method as we will need to override
     * the trigger button class. This should be removed as soon as possible !
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     */
    public function user_menu($user = null, $withlinks = null) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = 'usermenu nav-link';
        if (!$withlinks) {
            $usermenuclasses .= ' withoutlinks';
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            $returnstr = get_string('loggedinnot', 'moodle');
            if (!$loginpage) {
                $returnstr .= " (<a  href=\"$loginurl\"  class=\"text-light\" >" . get_string('login') . '</a>)';
            }
            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );

        }

        // If logged in as a guest user, show a string to that effect.
        if (isguestuser()) {
            $returnstr = get_string('loggedinasguest');
            if (!$loginpage && $withlinks) {
                $returnstr .= " (<a href=\"$loginurl\">" . get_string('login') . '</a>)';
            }

            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );
        }

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page);

        $avatarclasses = "avatars";
        $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
        $usertextcontents = $opts->metadata['userfullname'];

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $avatarcontents .= html_writer::span(
                $opts->metadata['realuseravatar'],
                'avatar realuser'
            );
            $usertextcontents = $opts->metadata['realuserfullname'];
            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                        $opts->metadata['userfullname'],
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['rolename'],
                'meta role role-' . $role
            );
        }

        // User login failures.
        if (!empty($opts->metadata['userloginfail'])) {
            $usertextcontents .= html_writer::span(
                $opts->metadata['userloginfail'],
                'meta loginfailures'
            );
        }

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        $returnstr .= html_writer::span(
            html_writer::span($usertextcontents, 'usertext mr-1') .
            html_writer::span($avatarcontents, $avatarclasses),
            'userbutton'
        );

        // Create a divider (well, a filler).
        $divider = new action_menu_filler();
        $divider->primary = false;

        $am = new action_menu();
        $am->set_menu_trigger(
            $returnstr,
            'nav-link' // CL BOOST : this is the only change here !
        );
        $am->set_action_label(get_string('usermenu'));
        $am->set_alignment(action_menu::TR, action_menu::BR);
        $am->set_nowrap_on_items();
        if ($withlinks) {
            $navitemcount = count($opts->navitems);
            $idx = 0;
            foreach ($opts->navitems as $key => $value) {

                switch ($value->itemtype) {
                    case 'divider':
                        // If the nav item is a divider, add one and skip link processing.
                        $am->add($divider);
                        break;

                    case 'invalid':
                        // Silently skip invalid entries (should we post a notification?).
                        break;

                    case 'link':
                        // Process this as a link item.
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) {
                            $pix = new pix_icon($value->pix, '', null, array('class' => 'iconsmall'));
                        } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                    $value->imgsrc,
                                    $value->title,
                                    array('class' => 'iconsmall')
                                ) . $value->title;
                        }

                        $al = new action_menu_link_secondary(
                            $value->url,
                            $pix,
                            $value->title,
                            array('class' => 'icon')
                        );
                        if (!empty($value->titleidentifier)) {
                            $al->attributes['data-title'] = $value->titleidentifier;
                        }
                        $am->add($al);
                        break;
                }

                $idx++;

                // Add dividers after the first item and before the last item.
                if ($idx == 1 || $idx == $navitemcount - 1) {
                    $am->add($divider);
                }
            }
        }

        return html_writer::div(
            $this->render($am),
            $usermenuclasses
        );
    }

    /**
     * @var Mustache_Engine $mustache The mustache template compiler
     */
    private $mustache;

    /**
     * Return an instance of the mustache class, but override the template finder to get
     * info from presets.
     *
     * @since 2.9
     * @return Mustache_Engine
     */
    protected function get_mustache() {
        global $CFG;

        if ($this->mustache === null) {
            require_once("{$CFG->libdir}/filelib.php");

            $themename = $this->page->theme->name;
            $themerev = theme_get_revision();

            // Create new localcache directory.
            $cachedir = make_localcache_directory("mustache/$themerev/$themename");

            // Remove old localcache directories.
            $mustachecachedirs = glob("{$CFG->localcachedir}/mustache/*", GLOB_ONLYDIR);
            foreach ($mustachecachedirs as $localcachedir) {
                $cachedrev = [];
                preg_match("/\/mustache\/([0-9]+)$/", $localcachedir, $cachedrev);
                $cachedrev = isset($cachedrev[1]) ? intval($cachedrev[1]) : 0;
                if ($cachedrev > 0 && $cachedrev < $themerev) {
                    fulldelete($localcachedir);
                }
            }

            // This is where we change the Loader for this theme.

            $loader = new \theme_clboost\output\mustache_filesystem_loader();
            $stringhelper = new \core\output\mustache_string_helper();
            $quotehelper = new \core\output\mustache_quote_helper();
            $jshelper = new \core\output\mustache_javascript_helper($this->page);
            $pixhelper = new \core\output\mustache_pix_helper($this);
            $shortentexthelper = new \core\output\mustache_shorten_text_helper();
            $userdatehelper = new \core\output\mustache_user_date_helper();

            // We only expose the variables that are exposed to JS templates.
            $safeconfig = $this->page->requires->get_config_for_javascript($this->page, $this);

            // Additional information used in the templates.
            $additionalinfo = $this->get_template_additional_information();
            // End additional info.
            $helpers = array('config' => $safeconfig,
                'str' => array($stringhelper, 'str'),
                'quote' => array($quotehelper, 'quote'),
                'js' => array($jshelper, 'help'),
                'pix' => array($pixhelper, 'pix'),
                'shortentext' => array($shortentexthelper, 'shorten'),
                'userdate' => array($userdatehelper, 'transform'),
                'additionalinfo' => $additionalinfo
            );

            $this->mustache = new \core\output\mustache_engine(array(
                'cache' => $cachedir,
                'escape' => 's',
                'loader' => $loader,
                'helpers' => $helpers,
                'pragmas' => [\Mustache_Engine::PRAGMA_BLOCKS],
                // Don't allow the JavaScript helper to be executed from within another
                // helper. If it's allowed it can be used by users to inject malicious
                // JS into the page.
                'blacklistednestedhelpers' => ['js']));

        }

        return $this->mustache;
    }

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
            $logourl =new \moodle_url("/theme/{$this->page->theme->name}/pix/logo.png");
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
            $compactlogourl = new \moodle_url("/theme/{$this->page->theme->name}/pix/logo-compact.png");
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

    /**
     * This will return the current template finder.
     *
     * @return mixed
     */
    public function get_mustache_template_finder() {
        return new \theme_clboost\output\mustache_template_finder();
    }

}