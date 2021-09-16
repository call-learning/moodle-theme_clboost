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
 * Unit tests for theme_clboost\setup_utils
 *
 * @package   theme_clboost
 * @category   test
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_clboost\setup_utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for theme_clboost\setup_utils
 *
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_clboost_setup_utils_test extends advanced_testcase {

    /**
     * Test real theme path
     *
     */
    public function test_set_virtual_global_page() {
        $this->resetAfterTest();
        global $PAGE;
        $oldpage = $PAGE;
        $page = setup_utils::set_virtual_global_page(false, 'standard', 'general', 'content', null, 3);
        $this->assertNotEquals($oldpage, $PAGE);
        $this->assertEquals($page, $PAGE);
        $this->assertEquals(3, $page->subpage);

    }

    /**
     * Convert from config
     */
    public function test_upload_file() {
        $this->resetAfterTest();
        global $CFG;
        $context = context_system::instance();
        $component = 'theme_clboost';
        $filearea = 'test';
        $itemid = 0;
        // Delete all test files.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id,
            $component,
            $filearea);

        setup_utils::upload_file(
            $context->id,
            $component,
            $filearea,
            $itemid,
            'theme/clboost/tests/fixtures/sample.jpg',
            'sample.jpg');
        $allareafiles = $fs->get_area_files($context->id, 'theme_clboost', 'test');
        $this->assertCount(2, $allareafiles);
        $this->assertTrue(in_array('sample.jpg', array_map(function($areafile) {
            return $areafile->get_filename();
        }, $allareafiles)));
    }

    /**
     * Setup page block
     */
    public function test_setup_page_blocks() {
        global $CFG, $OUTPUT;
        $this->resetAfterTest();
        $page = setup_utils::set_virtual_global_page(false, 'standard', 'general',
            'content', null, 3);
        setup_utils::setup_page_blocks($page,
            [
                [
                    'blockname' => 'html',
                    'showinsubcontexts' => '0',
                    'defaultregion' => 'content',
                    'defaultweight' => '4',
                    'files' => [
                        'sample.jpg' => [
                            'filepath' => 'theme/clboost/tests/fixtures/sample.jpg',
                        ]
                    ],
                    'configdata' => [
                        'text' => 'A text with image <img src="@@PLUGINFILE@@/sample.jpg">',
                        'title' => 'Title',
                        'classes' => 'a class',
                        'format' => '1'
                    ]
                ]
            ]
        );
        $otherpage = setup_utils::set_virtual_global_page(false, 'standard', 'general',
            'content', null, 3);
        $otherpage->blocks->load_blocks();
        $blocks = $otherpage->blocks->get_content_for_region('content', $OUTPUT);
        $this->assertCount(1, $blocks);
    }
}
