=== Easy Plugin Stats ===
Author URI:        https://www.nickdiego.com/
Contributors:      ndiego, outermostdesign
Tags:              plugin, stats, statistics, active installs, downloads
Requires at least: 6.5
Tested up to:      6.6
Requires PHP:      8.0
Stable tag:        2.0.0
License:           GPL-2.0
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Easily display stats associated with plugins hosted on WordPress.org.

== Description ==

Easily display stats associated with plugins hosted on WordPress.org, such as the number of downloads, active installations, star rating, and more.

This plugin was designed for developers with plugins in the WordPress.org repository (and anyone else) who want to display their plugin information on an external site. It uses the WordPress Plugins API to fetch the raw data, which can then be inserted into a page or post using a custom block, a Button block variation, or a shortcode. You can display stats from a single plugin, or aggregate stats from multiple plugins.

=== Available Stats === 

Nearly all of the fields returned by the WordPress.org Plugins API are available, as well as a few extra options. There are currently 28 to choose from.

**Single Stats**

* Active installs
* Times downloaded
* Plugin name
* Plugin slug
* Version
* Author
* Contributors
* Tags
* Requires
* Tested
* Number of reviews
* Rating out of 100
* Rating out of 5
* Star rating
* Last updated
* Date added
* Plugin homepage link
* Download link
* Live preview link
* Support forum link
* Reviews link
* Author profile link
* Donate link
* Description (Shortcode only)
* Installation (Shortcode only)
* Screenshots (Shortcode only)
* Changelog (Shortcode only)
* FAQ (Shortcode only)

**Aggregate Stats**

* Active installs
* Times downloaded

=== Stay Connected ===

* [View on GitHub](https://github.com/ndiego/easy-plugin-stats)
* [Visit plugin project page](https://nickdiego.com/projects/easy-plugin-stats/)
* [Follow on Twitter](https://twitter.com/nickmdiego)

== Installation ==

1. You have a couple of options:
	* Go to Plugins &rarr; Add New and search for "Easy Plugin Stats". Once found, click "Install".
	* Download the Easy Plugin Stats from WordPress.org and make sure the folder is zipped. Then upload via Plugins &rarr; Add New &rarr; Upload.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Search for the block within the Block Editor (Gutenberg) and begin using it. See the [FAQs](https://wordpress.org/plugins/easy-plugin-stats/#faq) for additional usage information.

== Frequently Asked Questions ==

= How do I use the Plugin Stat block? = 

Search for "Plugin Stat" in the block inserter and add it to a post just like you would any other block. In the inspector, enter the plugin slug and choose the stat you wish to display.

The slug can be retrieved from the plugin's URL on WordPress.org. For example, this plugin's slug is `easy-plugin-stats`.

= How do I use the Plugin Button variation for the Button block? = 

Insert a WordPress Buttons block, then click on the `+` inserter or open the block inserter. Besides the default Button block, you'll see the Plugin Button variation. Insert the Plugin Button into the Buttons block. In the inspector, enter the plugin slug and choose the link you wish to connect to the button.

The slug can be retrieved from the plugin's URL on WordPress.org. For example, this plugin's slug is `easy-plugin-stats`.

= How do I use the shortcode? = 

Below is the base shortcode structure with defaults. Technically, `slug` is the only required field. If you enter your plugin's slug and place the shortcode in a post or template, it will display the number of active installs and cache the data for `43200` seconds (12 hours). 

```
[eps type="single" slug="your-plugin-slug" field="active_installs" cache="43200" before="" after=""]
```

**type** 

*Optional.* The type of stat you with to display, either a stat from a single plugin or an aggregate stat generated from multiple plugins. Accepts `single` or `aggregate`. Defaults to `single`.

**slug** 

*Required.* This is the slug which can be retrieved from the plugin's URL on WordPress.org. For example, this plugin's slug is `easy-plugin-stats`. Accepts any valid plugin slug for `single`, or any number of space-separated plugin slugs when using `aggregate`. Defaults to `null`.

**field** 

*Optional.* The name of the field you have chosen to display. Accepts any of the following fields. Defaults to `active_installs`.

The available stat fields for `single` are:

* `active_installs` – Active installs
* `downloaded` – Times downloaded
* `name` – Plugin Name
* `slug` – Plugin Slug
* `version` – Version
* `author` – Author
* `contributors` – Contributors
* `tags` – Tags
* `requires` – Requires
* `tested` – Tested
* `num_ratings` – Number of reviews
* `rating` – Rating out of 100
* `five_rating` – Rating out of 5
* `star_rating` – Star rating
* `last_updated` – Last updated
* `added` – Date added
* `homepage_link` – Plugin homepage link
* `download_link` – Download link
* `live_preview_link` – Live preview link
* `support_link` – Support forum link
* `reviews_link` – Reviews link
* `author_profile` – Author profile link
* `donate_link` – Donate link
* `description` – Description
* `installation` – Installation
* `screenshots` – Screenshots
* `changelog` – Changelog
* `faq` – FAQ

The available stat fields for `aggregate` are:

* `active_installs` – Active installs
* `downloaded` – Times downloaded

**cache** 

*Optional.* The shortcode requests your plugin's stats from WordPress.org. To limit the number of requests made, response data is cached. This optional setting allows you to adjust the cache time as you see fit. Accepts any positive integer (representing seconds) greater than `5`. Defaults to `43200` (i.e. 12 hours).

**before**

*Optional.* Optional HTML to be printed before the field's output. Accepts any valid HTML, but note that all double quotes will be replaced with single quotes to adhere to the required shortcode markup. Defaults to `null`.

**after** 

*Optional.* Optional HTML to be printed after the field's output. Accepts any valid HTML, but note that all double quotes will be replaced with single quotes to adhere to the required shortcode markup. Defaults to `null`.

== Screenshots ==

1. A screenshot of the Plugin Stat block in Editor, which displays the total downloads for the Easy Plugin Stats plugin.
2. A screenshot of the Plugin Button block variation in Editor, which is connected to the Easy Plugin Stats download link.
3. A screenshot of the shortcode implementation in the Editor.

== Changelog ==

= 2.0.0 =

**Added**

* Add the Plugin Stat block.
* Add the Plugin Button block variation for the Button block.

**Changed**

* The TinyMCE plugin has been removed. Shortcodes still work but have to be created manually. 
* Star ratings are now SVGs, and the Dashicons have been removed.

= 1.0.0 =
* Initial Release

