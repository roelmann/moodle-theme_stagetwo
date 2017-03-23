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
 * StageTwo minimalist Boost child theme.
 *
 * @package    theme_stagetwo
 * @copyright  2017 Richard Oelmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'stagetwo';
$THEME->parents = ['boost'];

$THEME->sheets = [];

$THEME->scss = function($theme) {
    return theme_stagetwo_get_main_scss_content($theme);
};

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;

// Legacy settings.
$THEME->enable_dock = false;
$THEME->yuicssmodules = array();
$THEME->editor_sheets = [];
