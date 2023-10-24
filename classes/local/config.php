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
namespace theme_clboost\local;

/**
 * Theme config. In one place.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config {
    /**
     * Get layout settings
     *
     * @return array[]
     */
    public static function get_layouts() {
        return [
            // Most backwards compatible layout without the blocks - this is the layout used by default.
            'base' => [
                'file' => 'drawers.php',
                'regions' => [],
            ],
            // Standard layout with blocks, this is recommended for most pages with general information.
            'standard' => [
                'file' => 'drawers.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
            ],
            // Main course page.
            'course' => [
                'file' => 'drawers.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
                'options' => ['langmenu' => true],
            ],
            'coursecategory' => [
                'file' => 'drawers.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
            ],
            // Part of course, typical for modules - default page layout if $cm specified in require_login().
            'incourse' => [
                'file' => 'drawers.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
            ],
            // The site home page.
            'frontpage' => [
                'file' => 'frontpage.php',
                'regions' => ['content'],
                'defaultregion' => 'content',
                'options' => ['nonavbar' => true],
            ],
            // Server administration scripts.
            'admin' => [
                'file' => 'drawers.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
            ],
            // My dashboard page.
            'mydashboard' => [
                'file' => 'drawers.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
                'options' => ['nonavbar' => true, 'langmenu' => true, 'nocontextheader' => true],
            ],
            // My public page.
            'mypublic' => [
                'file' => 'drawers.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
            ],
            'login' => [
                'file' => 'login.php',
                'regions' => [],
                'options' => ['langmenu' => true],
            ],
            // Pages that appear in pop-up windows - no navigation, no blocks, no header.
            'popup' => [
                'file' => 'columns1.php',
                'regions' => [],
                'options' => ['nofooter' => true, 'nonavbar' => true],
            ],
            // No blocks and minimal footer - used for legacy frame layouts only!
            'frametop' => [
                'file' => 'columns1.php',
                'regions' => [],
                'options' => ['nofooter' => true, 'nocoursefooter' => true],
            ],
            // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
            'embedded' => [
                'file' => 'embedded.php',
                'regions' => [],
            ],
            // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
            // This must not have any blocks, links, or API calls that would lead to database or cache interaction.
            // Please be extremely careful if you are modifying this layout.
            'maintenance' => [
                'file' => 'maintenance.php',
                'regions' => [],
            ],
            // Should display the content and basic headers only.
            'print' => [
                'file' => 'columns1.php',
                'regions' => [],
                'options' => ['nofooter' => true, 'nonavbar' => false],
            ],
            // The pagelayout used when a redirection is occuring.
            'redirect' => [
                'file' => 'embedded.php',
                'regions' => [],
            ],
            // The pagelayout used for reports.
            'report' => [
                'file' => 'drawers.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
            ],
            // The pagelayout used for safebrowser and securewindow.
            'secure' => [
                'file' => 'secure.php',
                'regions' => ['side-pre'],
                'defaultregion' => 'side-pre',
            ],
        ];
    }

    /**
     * Setup the theme config
     *
     * @param \theme_config $theme
     * @param string $themeparentname
     */
    public static function setup_config(&$theme, $themeparentname = 'clboost') {
        $themename = $theme->name;
        // Automatically add all sheets / CSS defined in this theme.
        $theme->sheets = static::get_all_stylesheets($themename);
        $theme->editor_sheets = [];
        $theme->editor_scss = ['editor'];
        $theme->usefallback = true;
        $theme->scss = function($theme) use ($themename) {
            $callback = static::get_theme_callback('get_main_scss_content', $themename);
            return $callback($theme);
        };

        $theme->layouts = static::get_layouts();

        if ($themeparentname != $theme->name) { // Avoid infinite loop.
            $theme->parents = [$themeparentname];
        }
        if ($themeparentname != 'clboost') {
            $theme->parents[] = 'clboost';
        }
        $theme->parents[] = 'boost';
        $theme->enable_dock = false;
        $theme->csstreepostprocessor = static::get_theme_callback('css_tree_post_processor', $themename);
        $theme->extrascsscallback = static::get_theme_callback('get_extra_scss', $themename);
        $theme->prescsscallback = static::get_theme_callback('get_pre_scss', $themename);
        $theme->precompiledcsscallback = static::get_theme_callback('get_precompiled_css', $themename);
        $theme->yuicssmodules = [];
        $theme->rendererfactory = 'theme_overridden_renderer_factory';
        $theme->requiredblocks = '';
        $theme->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
        $theme->iconsystem = \core\output\icon_system::FONTAWESOME;
        $theme->haseditswitch = true;
        $theme->usescourseindex = true;
        // By default, all boost theme do not need their titles displayed.
        $theme->activityheaderconfig = [
            'notitle' => true,
        ];
        return $theme;
    }

    /**
     * Get all stylesheets
     *
     * @param string $themename
     * @return array
     */
    protected static function get_all_stylesheets($themename) {
        global $CFG;
        $stylefolder = "{$CFG->dirroot}/theme/{$themename}/style";
        $stylesfiles = [];
        if (is_dir($stylefolder)) {
            $cdir = scandir($stylefolder);
            foreach ($cdir as $value) {
                if (!in_array($value, [".", ".."])) {
                    $filepath = $stylefolder . DIRECTORY_SEPARATOR . $value;
                    if (is_file($filepath)) {
                        $stylesfiles[] = basename($filepath, '.css');
                    }
                }
            }
        }
        return $stylesfiles;
    }

    /**
     * Get the theme callback
     *
     * @param string $functioname
     * @param string $themename
     * @return string
     */
    protected static function get_theme_callback($functioname, $themename) {
        $themefunction = "theme_{$themename}_{$functioname}";
        if (function_exists($themefunction)) {
            return $themefunction;
        }
        return 'theme_clboost_' . $functioname;
    }
}
