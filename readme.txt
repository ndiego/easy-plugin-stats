=== Easy Plugin Stats ===
Contributors: ndiego, outermostdesign
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5BQQ26BHVMEYW
Tags: plugin, stats, statistics, wordpress.org, active installs, developer, download, rating, wordpress
Requires at least: 3.6
Tested up to: 4.4.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily display stats about a plugin that resides in the WordPress.org repository. 

== Description ==

**Disclaimer:** This plugin is geared towards developers with plugins in the WordPress.org repository and anyone else that wants to easily display information about a plugin in the respiratory. Don't fit this criteria? Then this plugin will probably not be of much use to you. 

This plugin was designed to be as simple as possible, while still being very powerful. There is no settings page, just one shortcode and TinyMCE shortcode interface to help you generate codes on the fly. Check out the video below for a quick overview.  


= Plugin Highlights =
* Display 27 different pieces of plugin information
* No 
* Very lightwieght

= Available Fields =

You can display any of the fields returned by the WordPress.org plugins API, as well as a few extra options. There are currently 27 options.

* Plugin Name
* Plugin Slug
* Version
* Author
* Author Profile Link
* Contributors
* Requires
* Tested
* Compatibility
* Rating out of 100
* Rating out of 5
* Star Rating
* Number of Reviews
* Active Installs
* Times Downloaded
* Last Updated
* Date Added
* Plugin Homepage Link
* Short Description
* Description
* Installation
* Screenshots
* Change Log
* FAQ
* Download Link
* Tags
* Donate Link

If you have questions or would like to request additional features please let me know in the plugin support forum.

= What Easy Plugin Stats Doesn’t Do =

If you are looking for download charts/graphs, rating graphic breakdowns, etc. you will be disappointed. With the exception of the Star Rating option, this plugin basically just returns the raw data from the WordPress.org API. Styling is up to you. 

= Support This Plugin = 

There are a few ways you can help support the development of this plugin:

1. If you spot an error or bug, please let us know in the support forums. The issue will be diagnosed an a new release push out as soon as possible.
1. [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5BQQ26BHVMEYW). Time is money, and contributions from users like you really help us dedicate more hours to the continual development and support of this plugin.


== Installation ==

1. You have a couple options:
	* Go to Plugins->Add New and search for "Genesis Columns Advanced”. Once found, click "Install".
	* Download the folder from Wordpress.org and zip the folder. Then upload via Plugins->Add New->Upload.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. From the ‘Plugins’ page, head to a post/page and check out the new button in your editor.
4. If you have any implementation questions, please post in the plugin support forum.


== Frequently Asked Questions ==

= Why is there no option for five even columns, or columns broken down by fifths? =

This was a conscious decision in order for the plugin to stay inline with the column classes offered by the Genesis Framework. The informational page on Genesis’s column classes can be found on [Studiopress.com](http://my.studiopress.com/tutorials/content-column-classes/). As of Genesis v2.1.2, there unfortunately is no option for fifths. If this changes in the future, I will update this plugin. 

= What are the available shortcodes that this plugin generates = 

**Main Column Shortcodes**

The “first” shortcodes are used for the first column in a row of columns.

* [one-half-first] …Column Content… [/one-half-first]
* [one-half] …Column Content… [/one-half]
* [one-third-first] …Column Content… [/one-third-first]
* [one-third] …Column Content…[ /one-third]
* [one-fourth-first] …Column Content… [/one-fourth-first]
* [one-fourth] …Column Content… [/one-fourth]
* [one-sixth-first] …Column Content… [/one-sixth-first]
* [one-sixth] …Column Content… [/one-sixth]

**Utility Shortcodes**

* [clearfix] - Adds a clearfix
* [vertical-spacer] – Adds a clear as well as some vertical separation
* [columns-container] …Column Content… [/columns-container] – Useful for wrapping column rows

**Class Attribute**

All shortcodes accept a "class" attribute. This allows you to add classes to any column or utility function. Classes should be space separated. See below for usage examples:

* [one-half-first class="col1"] …Column Content… [/one-half-first]
* [clearfix class="class1 class2"]
* [columns-container class="wrapper-class"] …Column Content… [/columns-container]

Have an idea for another utility shortcode? Let us know in the support forums. 

== Screenshots ==

1. A screenshot of columns button in editor and available options. 
2. A screenshot of Advanced Options popup were you can select from all 35 column configurations.
3. A screenshot of the frontend featuring a two column and six column layout with all columns evenly spaced.
4. A screenshot of the frontend featuring an advanced three column and an advanced two column layout. 

== Changelog ==

= 1.0.0 =
* Initial Release

