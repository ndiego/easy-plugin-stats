## Welcome to Easy Plugin Stats

**Disclaimer:** This plugin is geared towards developers with plugins in the WordPress.org repository and anyone else that wants to easily display information about a plugin that is in the respiratory. Don't fit this criteria? Then this plugin will probably not be of much use to you. 

This plugin was designed to be as simple as possible, while still being very powerful. There is no settings page, just one shortcode and TinyMCE shortcode interface to help you generate codes on the fly. Check out the video below for a quick overview.  

https://www.youtube.com/watch?v=jzK7ZQ-0z4g

### Available Fields

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

#### What Easy Plugin Stats Doesn’t Do

If you are looking for download charts/graphs, rating graphic breakdowns, etc. you will be disappointed. With the exception of the Star Rating option, this plugin basically just returns the raw data from the WordPress.org API. Styling is up to you. 

This plugin is also not currently translation ready, but the upcoming version 1.1.0 will be...

#### Support This Plugin

There are a few ways you can help support the development of this plugin:

1. If you spot an error or bug, please let us know in the support forums. The issue will be diagnosed an a new release push out as soon as possible.
1. [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5BQQ26BHVMEYW). Time is money, and contributions from users like you really help us dedicate more hours to the continual development and support of this plugin.


## Installation

1. You have a couple options:
	* Go to Plugins->Add New and search for "Easy Plugin Stats”. Once found, click "Install".
	* Download the folder from WordPress.org and zip the folder. Then upload via Plugins->Add New->Upload.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. From the ‘Plugins’ page, head to a post/page and check out the new button in your editor.
4. If you have any implementation questions, please post in the plugin support forum.


## Frequently Asked Questions

#### How does the Easy Plugin Stats shortcode work? 

The empty Easy Plugin Stats shortcode looks like:

[eps slug="" field="" before="" after="" cache_time=""]

* **Slug:** *Required.* This is the plugin slug which can be retrieved from the plugin url on WordPress.org. For example, this plugin's slus is easy-plugin-stats
* **Field:** *Required.* The name of the field you have chosen to display. 
* **Before:** *Optional, defaults to null.* HTML to be printed before the field's output.
* **After:** *Optional, defaults to null.* HTML to be printed after the field's output.
* **Cache Time:** *Optional, defaults to 60 seconds.* The shortcode requests your plugin's stats from WordPress.org. To limit the number of requests made, response data is cached for 60 seconds by default. Adjust this cache time as you see fit, but it must be greater than 5 seconds.

Either use the plugin shortcode interface, or you can manually type the shortcode into any area of your website that support shortcodes such as the editor, text widget, etc. 

The available stat fields are:

* name *(Plugin Name)*
* slug *(Plugin Slug)*
* version *(Version)*
* author *(Author)*
* author_profile *(Author Profile Link)*
* contributors *(Contributors)*
* requires *(Requires)*
* tested *(Tested)*
* compatibility *(Compatibility)*
* rating *(Rating out of 100)*
* five_rating *(Rating out of 5)*
* star_rating *(Star Rating)*
* num_ratings *(Number of Reviews)*
* active_installs *(Active Installs)*
* downloaded *(Times Downloaded)*
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
* tags *(Tags)*
* donate_link (Donate Link)*

If you have questions or would like to request additional features please let me know in the plugin support forum.

## Changelog

#### 1.0.0
* Initial Release
