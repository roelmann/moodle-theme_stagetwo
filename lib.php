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
 * Theme functions.
 *
 * @package    theme_stagetwo
 * @copyright  2016 Frédéric Massart - FMCorz.net
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
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_stagetwo_get_extra_scss($theme) {
    return !empty($theme->settings->scss) ? $theme->settings->scss : '';
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
        'brandcolor' => ['brand-primary'],
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
/*        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets); */
    }



    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    // Now append the preset.
    $filename = $theme->settings->preset;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/stagetwo/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/stagetwo/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_stagetwo', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/stagetwo/scss/preset/default.scss');
    }

    return $scss;
}
