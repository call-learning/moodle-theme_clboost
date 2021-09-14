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

namespace theme_clboost;

use context_block;
use context_system;
use moodle_page;

defined('MOODLE_INTERNAL') || die();

/**
 * Utilities for setting up blocks, page and so on.
 *
 * @package   theme_clboost
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setup_utils {
    /**
     * Setup dashboard  - to be completed
     *
     * @param moodle_page $page
     * @param array $blockdeflist
     * @param string $regionname
     * @return bool
     * @throws \dml_transaction_exception
     */
    public static function setup_page_blocks($page, $blockdeflist, $regionname = 'content') {
        global $DB;
        $transaction = $DB->start_delegated_transaction(); // Do not commit transactions until the end.
        $blocks = $page->blocks;
        $blocks->add_regions(array($regionname), false);
        $blocks->set_default_region($regionname);
        $blocks->load_blocks();

        // Delete unecessary blocks.
        $centralblocks = $blocks->get_blocks_for_region($regionname);
        foreach ($centralblocks as $cb) {
            blocks_delete_instance($cb->instance);
        }
        // Add the blocks.
        foreach ($blockdeflist as $blockdef) {
            global $DB;
            $blockinstance = (object) $blockdef;
            if (isset($blockinstance->files)) {
                unset($blockinstance->files);
            }
            $blockinstance->parentcontextid = $page->context->id;
            $blockinstance->pagetypepattern = $page->pagetype;
            $subpage = $page->subpage;
            if (!empty($subpage)) {
                $blockinstance->subpagepattern = $subpage;
            }
            if (!empty($blockinstance->configdata)) {
                $blockinstance->configdata = base64_encode(serialize((object) $blockinstance->configdata));

            } else {
                $blockinstance->configdata = '';
            }
            $blockinstance->timecreated = time();
            $blockinstance->timemodified = $blockinstance->timecreated;
            $blockinstance->showinsubcontexts = $blockdef['showinsubcontexts'] ?? 0;
            $contextdefs = [];
            if (!empty($blockinstance->capabilities)) {
                $contextdefs = $blockinstance->capabilities;
                unset($blockinstance->capabilities);
            }

            $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);
            if (!empty($blockdef['files'])) {
                static::upload_files_in_block($blockinstance, $blockdef['files']);
            }
            // Ensure the block context is created.
            context_block::instance($blockinstance->id);

            // If the new instance was created, allow it to do additional setup.
            if ($block = block_instance($blockinstance->blockname, $blockinstance)) {
                $block->instance_create();
            }
            foreach ($contextdefs as $capability => $roles) {
                foreach ($roles as $rolename => $permission) {
                    $roleid = $DB->get_field('role', 'id', array('shortname' => $rolename));
                    if ($roleid) {
                        role_change_permission($roleid, $block->context, $capability, $permission);
                    }
                }
            }
        }
        $DB->commit_delegated_transaction($transaction);// Ok, we can commit.
        return true;
    }

    /**
     * Upload files in blocks
     *
     * @param object $blockinstance
     * @param array $files
     */
    public static function upload_files_in_block($blockinstance, $files) {
        global $DB, $CFG;
        $configdata = unserialize(base64_decode($blockinstance->configdata));
        $context = context_block::instance($blockinstance->id);
        foreach ($files as $filename => $filespec) {
            static::upload_file($context->id, 'block_' . $blockinstance->blockname, $filespec['filearea'] ?? 'content',
                $blockinstance->id, $CFG->dirroot .$filespec['filepath'], $filename);
            $textfields = $filespec['textfields'] ?? ['text'];
            static::adjust_plugin_file_url($configdata, $textfields, $context->id, 'block_' . $blockinstance->blockname,
                $filespec['filearea'] ?? 'content', $blockinstance->id, $filename);
        }
        $DB->update_record('block_instances',
            (object) [
                'id' => $blockinstance->id,
                'configdata' => base64_encode(serialize($configdata)),
                'timemodified' => time()
            ]);
    }

    /**
     * Upload a file
     *
     * @param int $contextid
     * @param string $component
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath directory name
     * @param string $filename
     * @return bool|\stored_file
     */
    public static function upload_file($contextid, $component, $filearea, $itemid, $filepath, $filename) {
        $fullpath = "$filepath/$filename";
        // Create an area to upload the file.
        $fs = get_file_storage();
        // Create a file from the string that we made earlier.
        if (!($file = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename))) {
            global $CFG;
            $file = $fs->create_file_from_pathname(
                [
                    'contextid' => $contextid,
                    'component' => $component,
                    'filearea' => $filearea,
                    'itemid' => $itemid,
                    'filepath' => '/',
                    'filename' => $filename
                ], $fullpath);
        }
        return $file;
    }

    /**
     * Adjust text content
     *
     * @param object $originalobject
     * @param array $textfields the list of text fields to adjust
     * @param $contextid
     * @param $component
     * @param $filearea
     * @param $itemid
     * @param $filepath
     * @param null $filename
     * @return mixed
     */
    public static function adjust_plugin_file_url(&$originalobject, $textfields, $contextid, $component, $filearea, $itemid,
        $filepath, $filename = null) {
        $textfieldstructure = new \stdClass();
        if (!empty($textfields)) {
            foreach ($textfields as $textfield) {
                $originalobject->{$textfield} =
                    file_rewrite_pluginfile_urls($originalobject->{$textfield},
                        'pluginfile.php',
                        $contextid,
                        $component,
                        $filearea,
                        $itemid
                    );
            }
        }
        return $originalobject;
    }

    /**
     * Set the global page to a newly created virtual page. Used to add block on this page.
     *
     * @param false $restoresettings
     * @param string $layout
     * @param string $type
     * @param string $regiontoadd
     * @param null $context
     * @param int $subpageid
     * @return moodle_page
     */
    public static function set_virtual_global_page($restoresettings = false, $layout = 'standard', $type = 'general',
        $regiontoadd = 'content', $context = null, $subpageid = 0) {
        static $oldpage = null;
        global $PAGE;
        if ($restoresettings && $oldpage) {
            $PAGE = $oldpage;
            $oldpage = null;
            return $PAGE;
        }
        // Setup Home page.
        $page = new moodle_page();
        $page->set_pagelayout($layout);
        $page->set_pagetype($type);
        $page->blocks->add_region($regiontoadd);
        if ($subpageid) {
            $page->set_subpage($subpageid);
        }
        $page->set_context(empty($context) ? context_system::instance() : $context);
        $oldpage = $PAGE;
        $PAGE = $page;
        return $page;
    }
}
