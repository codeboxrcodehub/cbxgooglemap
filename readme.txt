=== CBX Map for Google Map & OpenStreetMap ===
Contributors: codeboxr, manchumahara
Donate link: https://codeboxr.com
Tags: google map, openstreetmap, openstreet, gutenberg block, elementor addons
Requires at least: 5.3
Tested up to: 6.8
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy google map and open streetmap embed using shortcode, Responsive.

== Description ==

CBX Map is a WordPress plugin that helps to display Google map and OpenStreetMap inside worpress. It’s easy to use using shortcode and map loads responsive. From the plugin’s seeing create map, find adress and configure easily with just mouse click.

### CBX Map for Google Map & OpenStreetMap by [Codeboxr](https://codeboxr.com/product/cbx-google-map-for-wordpress/)

>📺 [Live Demo](https://codeboxr.net/wordpress/cbxgooglemap/codeboxr/) | 🌟 [Upgrade to PRO](https://codeboxr.com/product/cbx-google-map-for-wordpress/#downloadarea) | 📋 [Documentation](https://codeboxr.com/doc/cbxmap-doc/) | 👨‍💻 [Free Support](https://wordpress.org/support/plugin/cbxgooglemap/) | 🤴 [Pro Support](https://codeboxr.com/contact-us) | 📱 [Contact](https://codeboxr.com/contact-us/)

## 🛄 Core Plugin Features ##

*   Google MAP or Openstreep map(no api key needed)
*   Custom post type for map
*   Easy Shortcode
*   Works without custom post type using the same shortcode [cbxgooglemap]
*   Responsive with browser width and resize
*   Info window
*   Default global Setting
*   Meta field for custom post type
*   Easy geo complete feature while finding proper marker position in custom post type edit.
*   Easy copy shortcode with mouse click

**▶️ Watch Video**
[youtube https://www.youtube.com/watch?v=pxeGCNc9Be0]

### 🀄 Widgets ###

*   Classic Wedget (From v1.1.7)
*   Elementor page builder element/widget support
*   Gutenberg support (From v1.1.2)
*   WPBackery(VC) Support (From v1.1.6)


### 🧮 Shortcodes ###

The most short form of the shortcode is `[cbxgooglemap id="google map post id here"]` where id is post id of custom google map post type

We can use shortcode to display saved map (this plugin creates custom post type CBX Maps(cbxgooglemap) in admin to create maps as need) or can display map using custom attributes. For save map we need only one param `[cbxgooglemap id="google map post id here"]`

	id      = post id, can be empty
	--------------------------------
	We can also display map using custom attributes
	maptype = default 'roadmap', possible values, 'roadmap', 'satellite', 'hybrid',  'terrain'
	width   = numeric value, '%' accepted, no 'px', if only numeric value then px will be added automatically
	height  = nemeric value, no 'px'
	zoom    = default 8
	lat     = lattitude value, required
	lng     = longitude value, required
	heading = used for info window title
	website = website url that is linked to place name in popup info window, leave empty to ignore
	address = used for info window content
	scrollwheel = 1 enable , 0 disable, default 1 or comes from default config
	showinfo = 1 enable , 0 disable, default 1 or comes from default config, show popup window or not
	infow_open = 1 enable , 0 disable, default 1 or comes from default config, show popup as opened or on click
	mapicon = map icon url, leave empty to ignore


Let us know which new feature you except.

For pro addon features and shortcode see documentation.

## 💎 CBX Map for Google Map & OpenStreetMap Pro Features ##
👉 Get the [pro addon](https://codeboxr.com/product/cbx-google-map-for-wordpress/#downloadarea)

*  	Distance Search shortcode , map and list display
* 	Displays multiple markers from the maps post types in single map
* 	Make map public or not so that single map can be browse like post
*	Map Categories


**▶️ Watch Video**
[youtube https://www.youtube.com/watch?v=bTuysIg-mho]

### 🔩 Installation ###

This section describes how to install the plugin and get it working.
e.g.
1. Upload folder  `cbxgooglemap` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to CBX Google map setting, put google map api key (in google project console you need, js map api, geo coding api, e)
4. Place shortcode any where as need

== Frequently Asked Questions ==

= Is there any custom function to call directly ? =

Not at this moment

== Screenshots ==

1. CBX Map global setting
2. CBX Map global setting-2
3. CBX Map admin post listing
4. CBX Map admin single map edit
5. CBX Map admin single map edit -2
6. CBX Map frontend

== Changelog ==

= 2.0.0 =
* [new] Backend UI refreshed with latest design
* [new] Fully revamped
* [new] Setting save bug
* [improvement] WordPress 6.8 compatible

= 1.1.12 =
* [improvement] Backend UI improved.
* [fixed] Sanitization and escaping improved
* [improvement] PHP 8.2 compatible
* [improvement] WordPress 6.4.3 compatible
* [improvement] Frontend & Backend style improvement for settings and map
* [improvement] Translation fixed