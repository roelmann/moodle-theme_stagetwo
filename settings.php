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

if ($ADMIN->fulltree) {

    $settings = new theme_boost_admin_settingspage_tabs('themesettingstagetwo', get_string('configtitle', 'theme_stagetwo'));

    $page = new admin_settingpage('theme_stagetwo_general', get_string('generalsettings', 'theme_stagetwo'));

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
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_stagetwo/presetfiles';
    $title = get_string('presetfiles','theme_stagetwo');
    $description = get_string('presetfiles_desc', 'theme_stagetwo');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    $name = 'theme_stagetwo/brandcolor';
    $title = get_string('brandcolor', 'theme_stagetwo');
    $description = get_string('brandcolor_desc', 'theme_stagetwo');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    $page = new admin_settingpage('theme_stagetwo_advanced', get_string('advancedsettings', 'theme_stagetwo'));

    $setting = new admin_setting_configtextarea('theme_stagetwo/scsspre',
        get_string('rawscsspre', 'theme_stagetwo'), get_string('rawscsspre_desc', 'theme_stagetwo'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configtextarea('theme_stagetwo/scss', get_string('rawscss', 'theme_stagetwo'),
        get_string('rawscss_desc', 'theme_stagetwo'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
