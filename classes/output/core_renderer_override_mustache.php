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
use Mustache_Engine;

/**
 * Core renderer functionalities
 *
 * This trait is hopefully temporary. Here we override functions from core renderer
 * that would need to be broken down into more manageable (and configurable pieces)
 *
 * @package theme_clboost
 */
trait core_renderer_override_mustache {
    /**
     * @var Mustache_Engine $mustache The mustache template compiler
     */
    private $mustache;

    /**
     * Return an instance of the mustache class, but override the template finder to get
     * info from presets.
     *
     * @return Mustache_Engine
     * @since 2.9
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
            $cleanstringhelper = new \core\output\mustache_clean_string_helper();
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
            $helpers = ['config' => $safeconfig,
                'str' => [$stringhelper, 'str'],
                'cleanstr' => [$cleanstringhelper, 'cleanstr'],
                'quote' => [$quotehelper, 'quote'],
                'js' => [$jshelper, 'help'],
                'pix' => [$pixhelper, 'pix'],
                'shortentext' => [$shortentexthelper, 'shorten'],
                'userdate' => [$userdatehelper, 'transform'],
                'additionalinfo' => $additionalinfo,
            ];

            $this->mustache = new \core\output\mustache_engine([
                'cache' => $cachedir,
                'escape' => 's',
                'loader' => $loader,
                'helpers' => $helpers,
                'pragmas' => [\Mustache_Engine::PRAGMA_BLOCKS],
                // Don't allow the JavaScript helper to be executed from within another
                // helper. If it's allowed it can be used by users to inject malicious
                // JS into the page.
                'disallowednestedhelpers' => ['js'],
                // Disable lambda rendering - content in helpers is already rendered, no need to render it again.
                'disable_lambda_rendering' => true,
            ]);

        }

        return $this->mustache;
    }
}
