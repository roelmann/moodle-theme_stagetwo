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
 * Main settings file.
 *
 * @package    theme_stagetwo
 * @copyright  2016 Richard Oelmann
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_stagetwo_admin_settingspage_tabs('themesettingstagetwo', get_string('configtitle', 'theme_stagetwo'));

    require('settings/presets_settings.php');
    require('settings/colours_settings.php');
    require('settings/header_settings.php');
    require('settings/content_settings.php');
    require('settings/socialicons_settings.php');

}
