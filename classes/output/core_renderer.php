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

namespace theme_stagetwo\output;

use coding_exception;
use html_writer;
use tabobject;
use tabtree;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use single_select;
use paging_bar;
use url_select;
use context_course;
use pix_icon;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_stagetwo
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \core_renderer {

    /** @var custom_menu_item language The language menu if created */
    protected $language = null;

    /**
     * Outputs the opening section of a box.
     *
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes An array of other attributes to give the box.
     * @return string the HTML to output.
     */
    public function box_start($classes = 'generalbox', $id = null, $attributes = array()) {
        if (is_array($classes)) {
            $classes = implode(' ', $classes);
        }
        return parent::box_start($classes . ' p-y-1', $id, $attributes);
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {

        global $CFG, $COURSE;

        // Get course overview files.
        if (empty($CFG->courseoverviewfileslimit)) {
            return array();
        }
        require_once($CFG->libdir. '/filestorage/file_storage.php');
        require_once($CFG->dirroot. '/course/lib.php');
        $fs = get_file_storage();
        $context = context_course::instance($COURSE->id);
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

        // Get course overview files as images - set $courseimage.
        // The loop means that the LAST stored image will be the one displayed if >1 image file.
        $courseimage = '';
        foreach ($files as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $courseimage = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            }
        }

        // Create html for header.
        $html = html_writer::start_tag('header', array('id' => 'page-header', 'class' => 'row'));
        $html .= html_writer::start_div('col-xs-12 p-a-1');
        $html .= html_writer::start_div('card');

        // If course image display it in separate div to allow css styling of inline style.
        if ($courseimage) {
            $html .= html_writer::start_div('withimage', array(
                'style' => 'background: url("'.$courseimage.'"); background-size: 100% 100%;
                width: 100%; height: 100%;'));
        }

        $html .= html_writer::start_div('card-block');
        $html .= html_writer::div($this->context_header_settings_menu(), 'pull-xs-right context-header-settings-menu');
        $html .= $this->context_header();
        $html .= html_writer::start_div('clearfix', array('id' => 'page-navbar'));
        $html .= html_writer::tag('div', $this->navbar(), array('class' => 'breadcrumb-nav'));
        $html .= html_writer::div($this->page_heading_button(), 'breadcrumb-button');
        $html .= html_writer::end_div(); // End page-navbar.
        $html .= html_writer::tag('div', $this->course_header(), array('id' => 'course-header'));
        $html .= html_writer::end_div(); // End card-block.

        if ($courseimage) {
            $html .= html_writer::end_div(); // End withimage inline style div.
        }

        $html .= html_writer::end_div(); // End card.
        $html .= html_writer::end_div(); // End col-xs-12 p-a-1.
        $html .= html_writer::end_tag('header');
        return $html;
    }

    /**
     * The standard tags that should be included in the <head> tag
     * including a meta description for the front page
     *
     * @return string HTML fragment.
     */
    public function standard_head_html() {
        global $SITE, $PAGE;

        $output = parent::standard_head_html();
        if ($PAGE->pagelayout == 'frontpage') {
            $summary = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
            if (!empty($summary)) {
                $output .= "<meta name=\"description\" content=\"$summary\" />\n";
            }
        }

        return $output;
    }

    /*
     * This renders the navbar.
     * Uses bootstrap compatible html.
     */
    public function navbar() {
        return $this->render_from_template('core/navbar', $this->page->navbar);
    }

    /**
     * We don't like these...
     *
     */
    public function edit_button(moodle_url $url) {
        return '';
    }

    /**
     * Override to inject the logo.
     *
     * @param array $headerinfo The header info.
     * @param int $headinglevel What level the 'h' tag will be.
     * @return string HTML for the header bar.
     */
    public function context_header($headerinfo = null, $headinglevel = 1) {
        global $SITE;

        if ($this->should_display_main_logo($headinglevel)) {
            $sitename = format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID)));
            return html_writer::div(html_writer::empty_tag('img', [
                'src' => $this->get_logo_url(null, 75), 'alt' => $sitename]), 'logo');
        }

        return parent::context_header($headerinfo, $headinglevel);
    }

    /**
     * Get the compact logo URL.
     *
     * @return string
     */
    public function get_compact_logo_url($maxwidth = 100, $maxheight = 100) {
        return parent::get_compact_logo_url(null, 35);
    }

    /**
     * Whether we should display the main logo.
     *
     * @return bool
     */
    public function should_display_main_logo($headinglevel = 1) {
        global $PAGE;

        // Only render the logo if we're on the front page or login page and the we have a logo.
        $logo = $this->get_logo_url();
        if ($headinglevel == 1 && !empty($logo)) {
            if ($PAGE->pagelayout == 'frontpage' || $PAGE->pagelayout == 'login') {
                return true;
            }
        }

        return false;
    }
    /**
     * Whether we should display the logo in the navbar.
     *
     * We will when there are no main logos, and we have compact logo.
     *
     * @return bool
     */
    public function should_display_navbar_logo() {
        $logo = $this->get_compact_logo_url();
        return !empty($logo) && !$this->should_display_main_logo();
    }

    /*
     * Overriding the custom_menu function ensures the custom menu is
     * always shown, even if no menu items are configured in the global
     * theme settings page.
     */
    public function custom_menu($custommenuitems = '') {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    /**
     * We want to show the custom menus as a list of links in the footer on small screens.
     * Just return the menu object exported so we can render it differently.
     */
    public function custom_menu_flat() {
        global $CFG;
        $custommenuitems = '';

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $custommenu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        return $custommenu->export_for_template($this);
    }

    /*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
     */
    protected function render_custom_menu(custom_menu $menu) {
        global $CFG;

        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

        return $content;
    }

    /**
     * This code renders the navbar button to control the display of the custom menu
     * on smaller screens.
     *
     * Do not display the button if the menu is empty.
     *
     * @return string HTML fragment
     */
    public function navbar_button() {
        global $CFG;

        if (empty($CFG->custommenuitems) && $this->lang_menu() == '') {
            return '';
        }

        $iconbar = html_writer::tag('span', '', array('class' => 'icon-bar'));
        $button = html_writer::tag('a', $iconbar . "\n" . $iconbar. "\n" . $iconbar, array(
            'class'       => 'btn btn-navbar',
            'data-toggle' => 'collapse',
            'data-target' => '.nav-collapse'
        ));
        return $button;
    }

    /**
     * Renders tabtree
     *
     * @param tabtree $tabtree
     * @return string
     */
    protected function render_tabtree(tabtree $tabtree) {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $data = $tabtree->export_for_template($this);
        return $this->render_from_template('core/tabtree', $data);
    }

    /**
     * Renders tabobject (part of tabtree)
     *
     * This function is called from {@link core_renderer::render_tabtree()}
     * and also it calls itself when printing the $tabobject subtree recursively.
     *
     * @param tabobject $tabobject
     * @return string HTML fragment
     */
    protected function render_tabobject(tabobject $tab) {
        throw new coding_exception('Tab objects should not be directly rendered.');
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(block_contents $bc, $region) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }

        $id = !empty($bc->attributes['id']) ? $bc->attributes['id'] : uniqid('block-');
        $context = new stdClass();
        $context->skipid = $bc->skipid;
        $context->blockinstanceid = $bc->blockinstanceid;
        $context->dockable = $bc->dockable;
        $context->id = $id;
        $context->hidden = $bc->collapsible == block_contents::HIDDEN;
        $context->skiptitle = strip_tags($bc->title);
        $context->showskiplink = !empty($context->skiptitle);
        $context->arialabel = $bc->arialabel;
        $context->ariarole = !empty($bc->attributes['role']) ? $bc->attributes['role'] : 'complementary';
        $context->type = $bc->attributes['data-block'];
        $context->title = $bc->title;
        $context->content = $bc->content;
        $context->annotation = $bc->annotation;
        $context->footer = $bc->footer;
        $context->hascontrols = !empty($bc->controls);
        if ($context->hascontrols) {
            $context->controls = $this->block_controls($bc->controls, $id);
        }

        return $this->render_from_template('core/block', $context);
    }

    /**
     * Returns the CSS classes to apply to the body tag.
     *
     * @since Moodle 2.5.1 2.6
     * @param array $additionalclasses Any additional classes to apply.
     * @return string
     */
    public function body_css_classes(array $additionalclasses = array()) {
        return $this->page->bodyclasses . ' ' . implode(' ', $additionalclasses);
    }

    /**
     * Renders preferences groups.
     *
     * @param  preferences_groups $renderable The renderable
     * @return string The output.
     */
    public function render_preferences_groups(preferences_groups $renderable) {
        return $this->render_from_template('core/preferences_groups', $renderable);
    }

    /**
     * Renders an action menu component.
     *
     * @param action_menu $menu
     * @return string HTML
     */
    public function render_action_menu(action_menu $menu) {

        // We don't want the class icon there!
        foreach ($menu->get_secondary_actions() as $action) {
            if ($action instanceof \action_menu_link && $action->has_class('icon')) {
                $action->attributes['class'] = preg_replace('/(^|\s+)icon(\s+|$)/i', '', $action->attributes['class']);
            }
        }

        if ($menu->is_empty()) {
            return '';
        }
        $context = $menu->export_for_template($this);

        // We do not want the icon with the caret, the caret is added by Bootstrap.
        if (empty($context->primary->menutrigger)) {
            $newurl = $this->pix_url('t/edit', 'moodle');
            $context->primary->icon['attributes'] = array_reduce($context->primary->icon['attributes'],
                function($carry, $item) use ($newurl) {
                    if ($item['name'] === 'src') {
                        $item['value'] = $newurl->out(false);
                    }
                    $carry[] = $item;
                    return $carry;
                }, []
            );
        }

        return $this->render_from_template('core/action_menu', $context);
    }

    /**
     * Implementation of user image rendering.
     *
     * @param help_icon $helpicon A help icon instance
     * @return string HTML fragment
     */
    protected function render_help_icon(help_icon $helpicon) {
        $context = $helpicon->export_for_template($this);
        return $this->render_from_template('core/help_icon', $context);
    }

    /**
     * Renders a single button widget.
     *
     * This will return HTML to display a form containing a single button.
     *
     * @param single_button $button
     * @return string HTML fragment
     */
    protected function render_single_button(single_button $button) {
        return $this->render_from_template('core/single_button', $button->export_for_template($this));
    }

    /**
     * Renders a single select.
     *
     * @param single_select $select The object.
     * @return string HTML
     */
    protected function render_single_select(single_select $select) {
        return $this->render_from_template('core/single_select', $select->export_for_template($this));
    }

    /**
     * Renders a paging bar.
     *
     * @param paging_bar $pagingbar The object.
     * @return string HTML
     */
    protected function render_paging_bar(paging_bar $pagingbar) {
        // Any more than 10 is not usable and causes wierd wrapping of the pagination in this theme.
        $pagingbar->maxdisplay = 10;
        return $this->render_from_template('core/paging_bar', $pagingbar->export_for_template($this));
    }

    /**
     * Renders a url select.
     *
     * @param url_select $select The object.
     * @return string HTML
     */
    protected function render_url_select(url_select $select) {
        return $this->render_from_template('core/url_select', $select->export_for_template($this));
    }

    /**
     * Renders a pix_icon widget and returns the HTML to display it.
     *
     * @param pix_icon $icon
     * @return string HTML fragment
     */
    protected function render_pix_icon(pix_icon $icon) {
        $data = $icon->export_for_template($this);
        foreach ($data['attributes'] as $key => $item) {
            $name = $item['name'];
            $value = $item['value'];
            if ($name == 'class') {
                $data['extraclasses'] = $value;
                unset($data['attributes'][$key]);
                $data['attributes'] = array_values($data['attributes']);
                break;
            }
        }
        return $this->render_from_template('core/pix_icon', $data);
    }

    /**
     * Renders the login form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $SITE;

        $context = $form->export_for_template($this);

        // Override because rendering is not supported in template yet.
        $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID)));

        return $this->render_from_template('core/login', $context);
    }

    /**
     * Render the login signup form into a nice template for the theme.
     *
     * @param mform $form
     * @return string
     */
    public function render_login_signup_form($form) {
        global $SITE;

        $context = $form->export_for_template($this);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context['logourl'] = $url;
        $context['sitename'] = format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID)));

        return $this->render_from_template('core/signup_form_layout', $context);
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the course administration, only on the course main page.
     *
     * @return string
     */
    public function context_header_settings_menu() {
        $context = $this->page->context;
        $menu = new action_menu();
        if ($context->contextlevel == CONTEXT_COURSE) {
            // Get the course admin node from the settings navigation.
            $items = $this->page->navbar->get_items();
            $node = end($items);
            $settingsnode = false;
            if (!empty($node) && $node->key === 'home') {
                $settingsnode = $this->page->settingsnav->find('frontpage', navigation_node::TYPE_SETTING);
                if ($settingsnode) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                    // We only add a list to the full settings menu if we didn't include every node in the short menu.
                    if ($skipped) {
                        $text = get_string('frontpagesettings');
                        $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                        $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
                        $menu->add_secondary_action($link);
                    }
                }
            } else if (!empty($node) && $node->type == navigation_node::TYPE_COURSE) {
                $settingsnode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
                if ($settingsnode) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                    // We only add a list to the full settings menu if we didn't include every node in the short menu.
                    if ($skipped) {
                        $text = get_string('courseadministration');
                        $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                        $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
                        $menu->add_secondary_action($link);
                    }
                }
            }
        } else if ($context->contextlevel == CONTEXT_USER) {
            $items = $this->page->navbar->get_items();
            $node = end($items);
            if (!empty($node) && ($node->key === 'myprofile')) {
                // Get the course admin node from the settings navigation.
                $node = $this->page->settingsnav->find('useraccount', navigation_node::TYPE_CONTAINER);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        }
        return $this->render($menu);
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the most specific thing from the settings block. E.g. Module administration.
     *
     * @return string
     */
    public function region_main_settings_menu() {
        $context = $this->page->context;
        $menu = new action_menu();

        if ($context->contextlevel == CONTEXT_MODULE) {

            $node = $this->page->navigation->find_active_node();
            $buildmenu = false;
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $buildmenu = true;
            } else if (!empty($node) && ($node->type == navigation_node::TYPE_ACTIVITY ||
                    $node->type == navigation_node::TYPE_RESOURCE)) {

                $items = $this->page->navbar->get_items();
                $navbarnode = end($items);
                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($navbarnode->key == $node->key && $navbarnode->type == $node->type) {
                    $buildmenu = true;
                }
            }
            if ($buildmenu) {
                // Get the course admin node from the settings navigation.
                $node = $this->page->settingsnav->find('modulesettings', navigation_node::TYPE_SETTING);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        }
        return $this->render($menu);
    }

    /**
     * Take a node in the nav tree and make an action menu out of it.
     * The links are injected in the action menu.
     *
     * @param action_menu $menu
     * @param navigation_node $node
     * @param boolean $indent
     * @param boolean $onlytopleafnodes
     * @return boolean nodesskipped - True if nodes were skipped in building the menu
     */
    private function build_action_menu_from_navigation(action_menu $menu,
                                                       navigation_node $node,
                                                       $indent = false,
                                                       $onlytopleafnodes = false) {
        $skipped = false;
        // Build an action menu based on the visible nodes from this navigation tree.
        foreach ($node->children as $menuitem) {
            if ($menuitem->display) {
                if ($onlytopleafnodes && $menuitem->children->count()) {
                    $skipped = true;
                    continue;
                }
                if ($menuitem->action) {
                    $text = $menuitem->text;
                    if ($menuitem->action instanceof action_link) {
                        $link = $menuitem->action;
                    } else {
                        $link = new action_link($menuitem->action, $menuitem->text, null, null, $menuitem->icon);
                    }
                    if ($indent) {
                        $link->add_class('m-l-1');
                    }
                } else {
                    if ($onlytopleafnodes) {
                        $skipped = true;
                        continue;
                    }
                    $link = $menuitem->text;
                }
                $menu->add_secondary_action($link);
                $skipped = $skipped || $this->build_action_menu_from_navigation($menu, $menuitem, true);
            }
        }
        return $skipped;
    }


    public function social_icons() {
        global $PAGE;
        $social_content = '';

        $hasfacebook    = (empty($PAGE->theme->settings->facebook)) ? false : $PAGE->theme->settings->facebook;
        $hastwitter     = (empty($PAGE->theme->settings->twitter)) ? false : $PAGE->theme->settings->twitter;
        $hasgoogleplus  = (empty($PAGE->theme->settings->googleplus)) ? false : $PAGE->theme->settings->googleplus;
        $haslinkedin    = (empty($PAGE->theme->settings->linkedin)) ? false : $PAGE->theme->settings->linkedin;
        $hasyoutube     = (empty($PAGE->theme->settings->youtube)) ? false : $PAGE->theme->settings->youtube;
        $hasflickr      = (empty($PAGE->theme->settings->flickr)) ? false : $PAGE->theme->settings->flickr;
        $hasvk          = (empty($PAGE->theme->settings->vk)) ? false : $PAGE->theme->settings->vk;
        $haspinterest   = (empty($PAGE->theme->settings->pinterest)) ? false : $PAGE->theme->settings->pinterest;
        $hasinstagram   = (empty($PAGE->theme->settings->instagram)) ? false : $PAGE->theme->settings->instagram;
        $hasskype       = (empty($PAGE->theme->settings->skype)) ? false : $PAGE->theme->settings->skype;
        $haswebsite     = (empty($PAGE->theme->settings->website)) ? false : $PAGE->theme->settings->website;
        $hasblog        = (empty($PAGE->theme->settings->blog)) ? false : $PAGE->theme->settings->blog;
        $hasvimeo       = (empty($PAGE->theme->settings->vimeo)) ? false : $PAGE->theme->settings->vimeo;
        $hastumblr      = (empty($PAGE->theme->settings->tumblr)) ? false : $PAGE->theme->settings->tumblr;
        $hassocial1     = (empty($PAGE->theme->settings->social1)) ? false : $PAGE->theme->settings->social1;
        $social1icon    = (empty($PAGE->theme->settings->socialicon1)) ? 'globe' : $PAGE->theme->settings->socialicon1;
        $hassocial2     = (empty($PAGE->theme->settings->social2)) ? false : $PAGE->theme->settings->social2;
        $social2icon    = (empty($PAGE->theme->settings->socialicon2)) ? 'globe' : $PAGE->theme->settings->socialicon2;
        $hassocial3     = (empty($PAGE->theme->settings->social3)) ? false : $PAGE->theme->settings->social3;
        $social3icon    = (empty($PAGE->theme->settings->socialicon3)) ? 'globe' : $PAGE->theme->settings->socialicon3;

        // If any of the above social networks are true, sets this to true.
        $hassocialnetworks = ($hasfacebook || $hastwitter || $hasgoogleplus || $hasflickr || $hasinstagram
            || $hasvk || $haslinkedin || $haspinterest || $hasskype || $haslinkedin || $haswebsite || $hasyoutube
            || $hasblog ||$hasvimeo || $hastumblr || $hassocial1 || $hassocial2 || $hassocial3) ? true : false;

        $social_content .= '<div class="socials_wrapper">';

        if ($hassocialnetworks) {
            $social_content .= '<ul class="socials">';
            if ($hasblog) {
                $social_content .='<a href="'.$hasblog.'" class="socialicon blog" title="'.$hasblog.'" alt="'.$hasblog.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-bookmark fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasblog.'</span>';
                $social_content .='</a>';
            }
            if ($haswebsite) {
                $social_content .='<a href="'.$haswebsite.'" class="socialicon website" title="'.$haswebsite.'" alt="'.$haswebsite.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-globe fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$haswebsite.'</span>';
                $social_content .='</a>';
            }
            if ($hasgoogleplus) {
                $social_content .='<a href="'.$hasgoogleplus.'" class="socialicon googleplus" title="'.$hasgoogleplus.'" alt="'.$hasgoogleplus.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-google-plus fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasgoogleplus.'</span>';
                $social_content .='</a>';
            }
            if ($hastwitter) {
                $social_content .='<a href="'.$hastwitter.'" class="socialicon twitter" title="'.$hastwitter.'" alt="'.$hastwitter.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-twitter fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hastwitter.'</span>';
                $social_content .='</a>';
            }
            if ($hasfacebook) {
                $social_content .='<a href="'.$hasfacebook.'" class="socialicon facebook" title="'.$hasfacebook.'" alt="'.$hasfacebook.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-facebook fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasfacebook.'</span>';
                $social_content .='</a>';
            }
            if ($haslinkedin) {
                $social_content .='<a href="'.$haslinkedin.'" class="socialicon linkedin" title="'.$haslinkedin.'" alt="'.$haslinkedin.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-linkedin fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$haslinkedin.'</span>';
                $social_content .='</a>';
            }
            if ($hasyoutube) {
                $social_content .='<a href="'.$hasyoutube.'" class="socialicon youtube" title="'.$hasyoutube.'" alt="'.$hasyoutube.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-youtube fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasyoutube.'</span>';
                $social_content .='</a>';
            }
            if ($hasvimeo) {
                $social_content .='<a href="'.$hasvimeo.'" class="socialicon vimeo" title="'.$hasvimeo.'" alt="'.$hasvimeo.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-vimeo-square fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasvimeo.'</span>';
                $social_content .='</button>';
            }
            if ($hasflickr) {
                $social_content .='<a href="'.$hasflickr.'" class="socialicon flickr" title="'.$hasflickr.'" alt="'.$hasflickr.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-flickr fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasflickr.'</span>';
                $social_content .='</a>';
            }
            if ($haspinterest) {
                $social_content .='<a href="'.$haspinterest.'" class="socialicon pinterest" title="'.$haspinterest.'" alt="'.$haspinterest.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-pinterest fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$haspinterest.'</span>';
                $social_content .='</a>';
            }
            if ($hastumblr) {
                $social_content .='<a href="'.$hastumblr.'" class="socialicon tumblr" title="'.$hastumblr.'" alt="'.$hastumblr.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-tumblr fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hastumblr.'</span>';
                $social_content .='</a>';
            }
            if ($hasinstagram) {
                $social_content .='<a href="'.$hasinstagram.'" class="socialicon instagram" title="'.$hasinstagram.'" alt="'.$hasinstagram.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-instagram fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasinstagram.'</span>';
                $social_content .='</a>';
            }
            if ($hasvk) {
                $social_content .='<a href="'.$hasvk.'" class="socialicon vk" title="'.$hasvk.'" alt="'.$hasvk.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-vk fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasvk.'</span>';
                $social_content .='</a>';
            }
            if ($hasskype) {
                $social_content .='<a href="skype:'.$hasskype.'?call" class="socialicon skype" title="'.$hasskype.'" alt="'.$hasskype.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-skype fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hasskype.'</span>';
                $social_content .='</a>';
            }
            if ($hassocial1) {
                $social_content .='<a href="'.$hassocial1.'" class="socialicon skype" title="'.$hassocial1.'" alt="'.$hassocial1.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-'.$social1icon.' fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hassocial1.'</span>';
                $social_content .='</a>';
            }
            if ($hassocial2) {
                $social_content .='<a href="'.$hassocial2.'" class="socialicon skype" title="'.$hassocial2.'" alt="'.$hassocial2.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-'.$social2icon.' fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hassocial2.'</span>';
                $social_content .='</a>';
            }
            if ($hassocial3) {
                $social_content .='<a href="'.$hassocial3.'" class="socialicon skype" title="'.$hassocial3.'" alt="'.$hassocial3.'">';
                $social_content .='<span class="fa-stack fa-2x">';
                $social_content .='<i class="fa fa-square fa-stack-2x"></i>';
                $social_content .='<i class="fa fa-'.$social3icon.' fa-inverse fa-stack-1x"></i>';
                $social_content .='</span>';
                $social_content .='<span class="sr-only">'.$hassocial3.'</span>';
                $social_content .='</a>';
            }
            $social_content .='</ul>';
        }
        $social_content .= '</div>';
        return $social_content;
    }

    public function footnote() {
        global $PAGE;
        $footnote = '';

        $footnote    = (empty($PAGE->theme->settings->footnote)) ? false : $PAGE->theme->settings->footnote;

        return $footnote;
    }

}
