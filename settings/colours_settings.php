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
 * Colours settings page file.
 *
 * @packagetheme_stagetwo
 * @copyright  2016 Richard Oelmann
 * @creditstheme_boost - MoodleHQ
 * @licensehttp://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('theme_stagetwo_colours', get_string('colours_settings', 'theme_stagetwo'));

// Raw SCSS to include before the content.
$setting = new admin_setting_configtextarea('theme_stagetwo/scsspre',
    get_string('rawscsspre', 'theme_stagetwo'), get_string('rawscsspre_desc', 'theme_stagetwo'), '', PARAM_RAW);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Variable $body-color.
// We use an empty default value because the default colour should come from the preset.
$name = 'theme_stagetwo/brandcolor';
$title = get_string('brandcolor', 'theme_stagetwo');
$description = get_string('brandcolor_desc', 'theme_stagetwo');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Variable $brandprimary.
$name = 'theme_stagetwo/brandprimary';
$title = get_string('brandprimary', 'theme_stagetwo');
$description = get_string('brandprimary_desc', 'theme_stagetwo');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Variable $brandsuccess.
$name = 'theme_stagetwo/brandsuccess';
$title = get_string('brandsuccess', 'theme_stagetwo');
$description = get_string('brandsuccess_desc', 'theme_stagetwo');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Variable $brandwarning.
$name = 'theme_stagetwo/brandwarning';
$title = get_string('brandwarning', 'theme_stagetwo');
$description = get_string('brandwarning_desc', 'theme_stagetwo');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Variable $branddanger.
$name = 'theme_stagetwo/branddanger';
$title = get_string('branddanger', 'theme_stagetwo');
$description = get_string('branddanger_desc', 'theme_stagetwo');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Variable $brandinfo.
$name = 'theme_stagetwo/brandinfo';
$title = get_string('brandinfo', 'theme_stagetwo');
$description = get_string('brandinfo_desc', 'theme_stagetwo');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Variable $graybase.
$name = 'theme_stagetwo/brandgraybase';
$title = get_string('brandgray', 'theme_stagetwo');
$description = get_string('brandgray_desc', 'theme_stagetwo');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


// Raw SCSS to include after the content.
$setting = new admin_setting_configtextarea('theme_stagetwo/scss', get_string('rawscss', 'theme_stagetwo'),
    get_string('rawscss_desc', 'theme_stagetwo'), '', PARAM_RAW);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
