STAGETWO
========

Stagetwo is a child theme for Moodle-theme_boost, the new core theme for Moodle3.2
Stagetwo is not designed for production use. It is being used to prove concepts and to
experiment with ways of creating new themes. This includes working with the new mustache
templates, renderers and classes and re-investigating how previously used settings and
tools and techniques can be applied within the new templates - preferably without adding
to the level of complexity.

Currently under development:

Additional settings -
---------------------
    Presets: stagetwo preset drop down will detect preset files uploaded by ftp as well
as those uploaded through the front end UI
    Colours: Additional colour pickers to fine tune preset styles, along with standard
raw scss custom settings
    Images: Default heading images, page and login backgrounds
    Content areas: Footnote html setting box
    Social Network Icons: Range of settings to specify social network links, including
several user defined

Custom course header images can be defined by setting a course summary file
---------------------------

Styling - 
---------
applied to login page, to make drawer semi-transparent etc. Note: Some styling is applied
simply to highlight areas being worked on or developed. These are NOT necessarily theme
colours!

Custom block regions - 
----------------------
	Alerts: is a top block that would stay at the top regardless of the screen size. It
is designed to have full width blocks, such as an html block, but with the block header
hidden
    Side-main-top: custom region within the main content 'card'. Blocks will arrange as
per flexbox layouts (including flexbox wrapping to accomodate small screens) - SMT will
remain above main content regardless of screen size.
	Side-main-bottom: custom region within the main content 'card'. Blocks will arrange
as per flexbox layouts (including flexbox wrapping to accomodate small screens)
    Side-main-right: custom region within the main content 'card'  - blocks laid out as
columns. Will drop down as flexbox layout wrap on smaller screens. When still smaller,
will drop down as flexbox column to correct order for responsiveness.
    Side-main-left: custom region within the main content 'card'  - blocks laid out as
columns. Will drop down as flexbox column on smaller screens to correct order when needed
for responsiveness. ie. Left, Main, Right content regions reorder as Main, Left, Right
vertically
