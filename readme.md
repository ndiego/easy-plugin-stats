## Easy Plugin Stats

![Easy Plugin Stats](https://github.com/ndiego/easy-plugin-stats/blob/main/_wordpress-org/banner-1544x500.png)

[![Active Installs](https://img.shields.io/wordpress/plugin/installs/easy-plugin-stats?logo=wordpress&logoColor=%23fff&label=Active%20Installs&labelColor=%23262626&color=%23262626)](https://wordpress.org/plugins/easy-plugin-stats/) [![Playground Demo Link](https://img.shields.io/wordpress/plugin/v/easy-plugin-stats?logo=wordpress&logoColor=%23fff&label=Playground%20Demo&labelColor=%233858e9&color=%233858e9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/ndiego/easy-plugin-stats/main/_playground/blueprint.json)

Easily display stats associated with plugins hosted on WordPress.org, such as the number of downloads, active installations, star rating, and more.

### Available Fields

You can display any of the fields returned by the WordPress.org plugins API, as well as a few extra options. There are currently 28 options.

* Active installs
* Times downloaded
* Plugin name
* Plugin slug
* Version
* Author
* Author profile link
* Contributors
* Requires
* Tested
* Compatibility
* Rating out of 100
* Rating out of 5
* Star rating
* Number of reviews
* Last updated
* Date added
* Plugin homepage link
* Description
* Installation
* Screenshots
* Changelog
* FAQ
* Download link
* Support link
* Tags
* Donate link

You can also display aggregate stats from multiple plugins. There are currently two supported aggregate fields.

* Active Installs
* Times Downloaded

If you have questions or would like to request additional features please let me know in the plugin support forum.

#### What Easy Plugin Stats Doesn’t Do

If you are looking for download charts/graphs, rating graphic breakdowns, etc. you will be disappointed. With the exception of the Star Rating option, this plugin basically just returns the raw data from the WordPress.org API. Styling is up to you. 

## Installation

1. You have a couple options:
	* Go to Plugins->Add New and search for "Easy Plugin Stats”. Once found, click "Install".
	* Download the folder from WordPress.org and zip the folder. Then upload via Plugins->Add New->Upload.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. From the ‘Plugins’ page, head to a post/page and check out the new button in your editor.
4. If you have any implementation questions, please post in the plugin support forum.


## Frequently Asked Questions

#### How do I add a plugin stat shortcode?

**Standard Method**

The easiest way is to click the new plugin stat icon that is added to the top bar of your WordPress editor. It looks like the standard WordPress plugin icon. This will launch a popup window where you can then choose your settings and enter in the desired plugin slug(s). Click insert, and the generated shortcode will be added to your editor's content field. 

**Manual Method**

If you don't want to bother with the popup window, or what to use the shortcode outside the editor, you can manually type out the shortcode. Below is the base shortcode structure with defaults. Technically, "slug" is the only required field. If you just enter your plugin's slug, the shortcode will display the number of active installs and cache the data for 43200 seconds (i.e. 12 hours). 

[eps type="single" slug="" field="active_installs" cache="43200" before="" after=""]

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

#### Where do I go to get more help?

If you have additional questions or would like to request additional features please let me know in the plugin [support forum](https://wordpress.org/support/plugin/easy-plugin-stats).

