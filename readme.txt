=== Easy Plugin Stats ===
Author URI:        https://www.nickdiego.com/
Contributors:      ndiego, outermostdesign
Tags:              plugin, stats, statistics, active installs, downloads
Requires at least: 6.3
Tested up to:      6.5
Requires PHP:      7.4
Stable tag:        2.0.0
License:           GPL-2.0
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Easily display stats associated with plugins hosted on WordPress.org.

== Description ==

**Disclaimer:** This plugin is geared towards developers with plugins in the WordPress.org repository and anyone else that wants to easily display information about a plugin that is in the repository. Don't fit this criteria? Then this plugin will probably not be of much use to you. 

This plugin was designed to be as simple as possible while still being very powerful. There is no settings page, just one shortcode and TinyMCE shortcode interface to help you generate codes with ease. You can display stats from a single plugin, or aggregate stats from multiple plugins. Check out the video below for a quick overview.

https://www.youtube.com/watch?v=zGFIlgcKS-s

= Available Fields =

You can display any of the fields returned by the WordPress.org plugins API, as well as a few extra options. There are currently 28 options.

* Active Installs
* Times Downloaded
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
* Last Updated
* Date Added
* Plugin Homepage Link
* Description
* Installation
* Screenshots
* Change Log
* FAQ
* Download Link
* Support Link
* Tags
* Donate Link

You can also display aggregate stats from multiple plugins. There are currently two supported aggregate fields.

* Active Installs
* Times Downloaded

If you have questions or would like to request additional features please let me know in the plugin support forum.

= What Easy Plugin Stats Doesn’t Do =

If you are looking for download charts/graphs, rating graphic breakdowns, etc. you will be disappointed. With the exception of the Star Rating option, this plugin basically just returns the raw data from the WordPress.org API. Styling is up to you. 

= Support This Plugin = 

There are a few ways you can help support the development of this plugin:

1. Rate this plugin on WordPress.org, preferably if you like it :)
1. If you spot an error or bug, please let us know in the support forums. The issue will be diagnosed an a new release push out as soon as possible.
1. [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5BQQ26BHVMEYW). Time is money, and contributions from users like you really help us dedicate more hours to the continual development and support of this plugin.


== Installation ==

1. You have a couple options:
	* Go to Plugins->Add New and search for "Easy Plugin Stats”. Once found, click "Install".
	* Download the folder from WordPress.org and zip the folder. Then upload via Plugins->Add New->Upload.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. From the 'Plugins' page, head to a post/page and check out the new button in your editor.
4. If you have any implementation questions, please post in the plugin support forum.


== Frequently Asked Questions ==

= How do I add a plugin stat shortcode? = 

**Standard Method**

The easiest way is to click the new plugin stat icon that is added to the top bar of your WordPress editor. It looks like the standard WordPress plugin icon. This will launch a popup window where you can then choose your settings and enter in the desired plugin slug(s). Click insert, and the generated shortcode will be added to your editor's content field. 

**Manual Method**

If you don't want to bother with the popup window, or what to use the shortcode outside the editor, you can manually type out the shortcode. Below is the base shortcode structure with defaults. Technically, "slug" is the only required field. If you just enter your plugin's slug, the shortcode will display the number of active installs and cache the data for 43200 seconds (i.e. 12 hours). 

`
[eps type="single" slug="" field="active_installs" cache="43200" before="" after=""]
`

**type** 

*Optional.* The type of stat you with to display, either a stat from a single plugin or an aggregate stat generated from multiple plugins. Accepts "single" or "aggregate". Defaults to "single".

**slug** 

*Required.* This is the plugin slug which can be retrieved from the plugin url on WordPress.org. For example, this plugin's slug is easy-plugin-stats. Accepts any valid plugin slug for "single", or any number of space-separated plugin slungs when using "aggregate". Defaults to null.

**field** 

*Optional.* The name of the field you have chosen to display. Accepts any of the following field. Defaults to "active_installs".

The available stat fields for "single" are:

* active_installs *(Active Installs)*
* downloaded *(Times Downloaded)*
* name *(Plugin Name)*
* slug *(Plugin Slug)*
* version *(Version)*
* author *(Author)*
* author_profile *(Author Profile Link)*
* contributors *(Contributors)*
* requires *(Requires)*
* tested *(Tested)*
* rating *(Rating out of 100)*
* five_rating *(Rating out of 5)*
* star_rating *(Star Rating)*
* num_ratings *(Number of Reviews)*
* last_updated *(Last Updated)*
* added *(Date Added)*
* homepage *(Plugin Homepage Link)*
* short_description *(Short Description)*
* description *(Description)*
* installation *(Installation)*
* screenshots *(Screenshots)*
* changelog *(Change Log)*
* faq *(FAQ)*
* download_link *(Download Link)*
* support_link *(Support Link)*
* tags *(Tags)*
* donate_link *(Donate Link)*

The available stat fields for "aggregate" are:

* active_installs *(Active Installs)*
* downloaded *(Times Downloaded)*

**cache** 

*Optional.* The shortcode requests your plugin's stats from WordPress.org. To limit the number of requests made, response data is cached. This optional setting allows you to adjust the cache time as you see fit. Accepts any positive integer (representing seconds) greater than 5. Defaults to "43200" (i.e. 12 hours).

**before**

*Optional.* Optional HTML to be printed before the field's output. Accepts any valid HTML, but note that all double quotes will be replaced with single quotes to adhere to required shortcode markup. Defaults to null.

**after** 

*Optional.* Optional HTML to be printed after the field's output. Accepts any valid HTML, but note that all double quotes will be replaced with single quotes to adhere to required shortcode markup. Defaults to null.

= Where do I go to get more help? =

If you have additional questions or would like to request additional features please let me know in the plugin [support forum](https://wordpress.org/support/plugin/easy-plugin-stats).

== Screenshots ==

1. A screenshot of the Easy Plugin Stats button in editor and some sample shortcodes. 
2. A screenshot of the Easy Plugin Stats popup which helps you generate shortcodes on the fly.
3. A screenshot of the frontend, which shows the output of the sample shortcodes in the first screenshot.

== Changelog ==

= 2.0.0 =

**Changed**

* The TinyMCE plugin has been removed. Shortcodes still work but have to be created manually. 
* Star ratings are now SVGs, and the Dashicons have been removed.

= 1.0.0 =
* Initial Release

