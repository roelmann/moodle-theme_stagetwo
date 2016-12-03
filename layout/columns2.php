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
 * A two column layout for the stagetwo theme.
 *
 * @package   theme_stagetwo
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

if (isloggedin() && !behat_is_test_site()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$blockshtml = '';
$blocksprehtml = $OUTPUT->blocks('side-pre');
$hasblocksprehtml = strpos($blocksprehtml, 'data-block=') !== false;

$blocksmaintophtml = $OUTPUT->blocks('side-main-top');
$hasblocksmaintophtml = strpos($blocksmaintophtml, 'data-block=') !== false;
$blocksmainbottomhtml = $OUTPUT->blocks('side-main-bottom');
$hasblocksmainbottomhtml = strpos($blocksmainbottomhtml, 'data-block=') !== false;

$blocksmainlefthtml = $OUTPUT->blocks('side-main-left');
$hasblocksmainlefthtml = strpos($blocksmainlefthtml, 'data-block=') !== false;
$blocksmainrighthtml = $OUTPUT->blocks('side-main-right');
$hasblocksmainrighthtml = strpos($blocksmainrighthtml, 'data-block=') !== false;
$blocksalertshtml = $OUTPUT->blocks('side-alerts');
$hasblocksalertshtml = strpos($blocksalertshtml, 'data-block=') !== false;
$hasblocks = false;
if ($hasblocksprehtml ||
    $hasblocksmaintophtml ||
    $hasblocksmainbottomhtml ||
    $hasblocksmainlefthtml ||
    $hasblocksmainrighthtml ||
    $hasblocksalertshtml) {
    $hasblocks = true;
}

$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID))),
    'output' => $OUTPUT,

    'hassidepre' => $hasblocksprehtml,
    'sidepreblocks' => $blocksprehtml,
    'hassidemaintop' => $hasblocksmaintophtml,
    'sidemaintopblocks' => $blocksmaintophtml,
    'hassidemainbottom' => $hasblocksmainbottomhtml,
    'sidemainbottomblocks' => $blocksmainbottomhtml,
    'hassidemainleft' => $hasblocksmainlefthtml,
    'sidemainleftblocks' => $blocksmainlefthtml,
    'hassidemainright' => $hasblocksmainrighthtml,
    'sidemainrightblocks' => $blocksmainrighthtml,
    'hassidealerts' => $hasblocksalertshtml,
    'sidealertsblocks' => $blocksalertshtml,
    'hasblocks' => $hasblocks,

    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu)
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;
echo $OUTPUT->render_from_template('theme_stagetwo/columns2', $templatecontext);

