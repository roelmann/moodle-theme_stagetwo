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
 * SCSS Lib file.
 *
 * @package    theme_stagetwo
 * @copyright  2016 Richard Oelmann
 * @credits    theme_stagetwo - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */
function theme_stagetwo_css_tree_post_processor($tree, $theme) {
    $prefixer = new theme_stagetwo\autoprefixer($tree);
    $prefixer->prefix();
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_stagetwo_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    $iterator = new DirectoryIterator($CFG->dirroot . '/theme/stagetwo/scss/preset/');
    $preset_isset = '';
    foreach ($iterator as $pfile) {
        if($pfile->isDot()) continue;
        $presetname = substr($pfile,0,strlen($pfile)-5); // name - '.scss'
        if ($filename == $presetname) {
            $scss .= file_get_contents($CFG->dirroot . '/theme/stagetwo/scss/preset/' . $pfile);
            $preset_isset = true;
        }
    }
    if (!$preset_isset) {
        $filename .= '.scss';
        if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_stagetwo', 'preset', 0    , '/', $filename))) {
            $scss .= $presetfile->get_content();
        } else {
            // Safety fallback - maybe new installs etc.
            $scss .= file_get_contents($CFG->dirroot . '/theme/stagetwo/scss/preset/default.scss');
        }
    }

    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_stagetwo_get_pre_scss($theme) {
    global $CFG;

    $scss = '';

    $configurable = [
    // Config key => variableName, ....
        'brandprimary' => ['brand-primary'],
        'brandsuccess' => ['brand-success'],
        'brandinfo' => ['brand-info'],
        'brandwarning' => ['brand-warning'],
        'branddanger' => ['brand-danger'],
        'brandgraybase' => ['gray-base'],
    ];

    // Add settings variables.
    foreach ($configurable as $configkey => $targets) {
        $value = $theme->settings->{$configkey};
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    // Set the default image for the header.
    $headerbg = $theme->setting_file_url('headerdefaultimage', 'headerdefaultimage');
    if (isset($headerbg)) {
        // Add a fade in transition to avoid the flicker on course headers ***.
        $scss .= 'header#page-header .card {background-image: url("'.$headerbg.'"); background-size:100% 100%;}';
    }

    // Set the background image for the page.
    $pagebg = $theme->setting_file_url('backgroundimage', 'backgroundimage');
    if (isset($pagebg)) {
        $scss .= 'body {background-image: url("'.$pagebg.'"); background-size:100% 100%;}';
    }

    // Set the background image for the login page.
    $loginbg = $theme->setting_file_url('loginimage', 'loginimage');
    if (isset($loginbg)) {
        $scss .= 'body#page-login-index {background-image: url("'.$loginbg.'"); background-size:100% 100%;}';
    }

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_stagetwo_get_extra_scss($theme) {
    // Adapted from Boost to allow other changes or settings if required.
    $extrascss = '';
    if (!empty($theme->settings->scss)) {
        $extrascss .= $theme->settings->scss;
    }

    return $extrascss;
}

