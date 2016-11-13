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
 * @package   theme_stagetwo
 * @copyright 2016 Richard Oelmann
 * @credits   2016 Theme_boost MoodleHQ
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

if ($ADMIN->fulltree) {
    $settings = new theme_stagetwo_admin_settingspage_tabs('themesettingstagetwo', get_string('configtitle', 'theme_stagetwo'));
    $page = new admin_settingpage('theme_stagetwo_general', get_string('generalsettings', 'theme_stagetwo'));

    // Preset.
    $name = 'theme_stagetwo/preset';
    $title = get_string('preset', 'theme_stagetwo');
    $description = get_string('preset_desc', 'theme_stagetwo');
    $choices[] = '';
    $iterator = new DirectoryIterator($CFG->dirroot . '/theme/stagetwo/scss');
    foreach ($iterator as $presetfile) {
        if (substr($presetfile,0,7) === 'preset-') {
            $presetname = substr($presetfile,7,strlen($presetfile)-12); // 'preset-' + '.scss'
            $choices[$presetname] = ucfirst($presetname);
        }
    }
    natsort($choices);
    $default = 'default';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $brandprimary.
    $name = 'theme_stagetwo/brandprimary';
    $title = get_string('brandprimary', 'theme_stagetwo');
    $description = get_string('brandprimary_desc', 'theme_stagetwo');
    $default = '';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $brandsuccess.
    $name = 'theme_stagetwo/brandsuccess';
    $title = get_string('brandsuccess', 'theme_stagetwo');
    $description = get_string('brandsuccess_desc', 'theme_stagetwo');
    $default = '';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $brandwarning.
    $name = 'theme_stagetwo/brandwarning';
    $title = get_string('brandwarning', 'theme_stagetwo');
    $description = get_string('brandwarning_desc', 'theme_stagetwo');
    $default = '';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $branddanger.
    $name = 'theme_stagetwo/branddanger';
    $title = get_string('branddanger', 'theme_stagetwo');
    $description = get_string('branddanger_desc', 'theme_stagetwo');
    $default = '';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $brandinfo.
    $name = 'theme_stagetwo/brandinfo';
    $title = get_string('brandinfo', 'theme_stagetwo');
    $description = get_string('brandinfo_desc', 'theme_stagetwo');
    $default = '';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_stagetwo_advanced', get_string('advancedsettings', 'theme_stagetwo'));

    // Raw SCSS for before the content.
    $setting = new theme_stagetwo_admin_setting_scss_variables('theme_stagetwo/scss_variables',
        get_string('scssvariables', 'theme_stagetwo'), get_string('scssvariables_desc', 'theme_stagetwo'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS for after the content.
    $setting = new admin_setting_configtextarea('theme_stagetwo/scss', get_string('rawscss', 'theme_stagetwo'),
        get_string('rawscss_desc', 'theme_stagetwo'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
