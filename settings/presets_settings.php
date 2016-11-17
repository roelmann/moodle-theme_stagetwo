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
 * Presets settings page file.
 *
 * @package    theme_stagetwo
 * @copyright  2016 Richard Oelmann
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_stagetwo_presets', get_string('presets_settings', 'theme_stagetwo'));

// Preset.
$name = 'theme_stagetwo/preset';
$title = get_string('preset', 'theme_stagetwo');
$description = get_string('preset_desc', 'theme_stagetwo');
$default = 'default.scss';

$context = context_system::instance();
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'theme_stagetwo', 'preset', 0, 'itemid, filepath, filename', false);

$choices = [];
foreach ($files as $file) {
    $choices[$file->get_filename()] = $file->get_filename();
}
// These are the built in presets.
$choices['default.scss'] = 'default.scss';
$choices['plain.scss'] = 'plain.scss';

$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Preset files setting.
$name = 'theme_stagetwo/presetfiles';
$title = get_string('presetfiles', 'theme_stagetwo');
$description = get_string('presetfiles_desc', 'theme_stagetwo');

$setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
array('maxfiles' => 20, 'accepted_types' => array('.scss')));
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
