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
 * Boost config.
 *
 * @package   theme_boost
 * @copyright 2016 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

$THEME->name = 'stagetwo';
$THEME->scssfile = 'preset';
$THEME->sheets = ['custom'];
$THEME->editor_sheets = ['editor'];

$THEME->parents = ['boost'];
$THEME->enable_dock = false;
$THEME->csstreepostprocessor = 'theme_stagetwo_css_tree_post_processor';
$THEME->extrascsscallback = 'theme_stagetwo_get_extra_scss';
$THEME->prescsscallback = 'theme_stagetwo_get_pre_scss';
$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = array();
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->undeletableblocktypes = '';