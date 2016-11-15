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
    // Changes for testing only - to differentiate between calling boost/child functions.
    $extrascss = '';
    if(!empty($theme->settings->scss)) {
        $extrascss .= $theme->settings->scss;
    }

    // Set the background image for the header.
    $headerbg = $theme->setting_file_url('courseimagedefaultheader', 'courseimagedefaultheader');

global $CFG, $COURSE;

if (empty($CFG->courseoverviewfileslimit)) {
    return array();
}
require_once($CFG->libdir. '/filestorage/file_storage.php');
require_once($CFG->dirroot. '/course/lib.php');
$fs = get_file_storage();

//--------------------------------------
$context = context_course::instance($COURSE->id); // ERROR HERE: picking up $COURSE->ID = 1 on all courses.
//--------------------------------------

$files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'filename', false);
if (count($files)) {
    $overviewfilesoptions = course_overviewfiles_options($COURSE->id);
    $acceptedtypes = $overviewfilesoptions['accepted_types'];
    if ($acceptedtypes !== '*') {
        // Filter only files with allowed extensions.
        require_once($CFG->libdir. '/filelib.php');
        foreach ($files as $key => $file) {
            if (!file_extension_in_typegroup($file->get_filename(), $acceptedtypes)) {
                unset($files[$key]);
            }
        }
    }
    if (count($files) > $CFG->courseoverviewfileslimit) {
        // Return no more than $CFG->courseoverviewfileslimit files.
        $files = array_slice($files, 0, $CFG->courseoverviewfileslimit, true);
    }
}

// Display course overview files.
$courseimage = '';
foreach ($files as $file) {
    $isimage = $file->is_valid_image();
    if ($isimage) {
        $courseimage = file_encode_url("$CFG->wwwroot/pluginfile.php",
            '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
            $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
    }
}

    $extrascss .= 'header#page-header .card {background-image: url("'.$headerbg.'");}';
    $extrascss .= 'header#page-header .card h1:after{content:": '.$COURSE->id.' : '.$context->id.' : '.count($files).$files.'"}';


    return $extrascss;
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

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */

function theme_stagetwo_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('stagetwo');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo')) {
        $theme = theme_config::load('stagetwo');
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
//    } else if (preg_match("/slide[1-9][0-9]*image/", $filearea) !== false) { // Carousel images.
//        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
//    } else if ($filearea === 'minilogo') {
//            return $theme->setting_file_serve('minilogo', $args, $forcedownload, $options);
//    } else if ($filearea === 'favicon') {
//            return $theme->setting_file_serve('favicon', $args, $forcedownload, $options);
//    } else if ($filearea === 'loginbg') {
//            return $theme->setting_file_serve('loginbg', $args, $forcedownload, $options);
    } else if ($filearea === 'courseimagedefaultheader') {
            return $theme->setting_file_serve('courseimagedefaultheader', $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * This function creates the dynamic HTML needed for some
 * settings and then passes it back in an object so it can
 * be echo'd to the page.
 *
 * This keeps the logic out of the layout files.
 *
 * @param string $setting bring the required setting into the function
 * @param bool $format
 * @param string $setting
 * @param array $theme
 * @param stdclass $CFG
 * @return string
 */
function theme_stagetwo_get_setting($setting, $format = false) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('stagetwo');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}
