## Easy Plugin Stats

![Easy Plugin Stats](https://github.com/ndiego/easy-plugin-stats/blob/main/_wordpress-org/banner-1544x500.png)

[![Active Installs](https://img.shields.io/wordpress/plugin/installs/easy-plugin-stats?logo=wordpress&logoColor=%23fff&label=Active%20Installs&labelColor=%23262626&color=%23262626)](https://wordpress.org/plugins/easy-plugin-stats/) [![Playground Demo Link](https://img.shields.io/wordpress/plugin/v/easy-plugin-stats?logo=wordpress&logoColor=%23fff&label=Playground%20Demo&labelColor=%233858e9&color=%233858e9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/ndiego/easy-plugin-stats/main/_playground/blueprint-github.json)

Easily display stats associated with plugins hosted on WordPress.org, such as the number of downloads, active installations, star rating, and more.

This plugin was designed for developers with plugins in the WordPress.org repository (and anyone else) who want to display their plugin information on an external site. It uses the WordPress Plugins API to fetch the raw data, which can then be inserted into a page or post using a custom block, a Button block variation, or a shortcode. You can display stats from a single plugin, or aggregate stats from multiple plugins.

### Available stats

Nearly all of the fields returned by the WordPress.org Plugins API are available, as well as a few extra options. There are currently 28 to choose from.

#### Single stats

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

#### Aggregate stats

Display combined stats from multiple plugins.

* Active installs
* Times downloaded

Refer to the plugin's [FAQs](https://wordpress.org/plugins/easy-plugin-stats/#faq) section on WordPress.org for additional usage information. 

## Requirements

- WordPress 6.5+
- PHP 8.0+

## Development

1. Set up a local WordPress development environment.
2. Clone / download this repository into the `wp-content/plugins` folder.
3. Navigate to the `wp-content/plugins/easy-plugin-stats` folder in the command line.
4. Run `npm install` to install the plugin's dependencies within a `/node_modules/` folder.
5. Run `composer install` to install the additional WordPress composer tools within a `/vendor/` folder.
6. Run `npm run start` to compile and watch source files for changes while developing.

Refer to `package.json` for additional commands.

