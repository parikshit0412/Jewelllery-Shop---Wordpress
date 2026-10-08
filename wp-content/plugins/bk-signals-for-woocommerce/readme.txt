=== BK Signals for WooCommerce ===
Contributors: bermikod
Tags: woocommerce, social proof, live visitors, sales popup, analytics
Requires at least: 6.2
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display WooCommerce social proof widgets, product activity counters, and recent sales notifications on product pages.

== Description ==

BK Signals for WooCommerce helps stores show product activity signals such as live visitors, product views, active carts, real sales counters, and recent sales popups.

The plugin is designed for product pages that need lightweight social proof without forcing heavy live queries, WebSockets, or constant database writes on every page load.

= Free Features =

* Live Visitors - show how many visitors are viewing a product right now.
* Views - display product view counts by time window.
* Active Carts - show current in-cart demand for each product.
* Sales - display WooCommerce purchase activity.
* Recent Sales Popup - rotate real recent purchase notifications.
* General Widget - combine views, active carts, sales, and live visitors in one block.
* Widget editor - create, edit, preview, and copy shortcodes.
* Design previews - choose from multiple frontend designs before publishing.
* Admin dashboard - view a quick product activity snapshot inside WordPress.

== Requirements ==

* WordPress 5.8 or higher
* PHP 7.4 or higher
* WooCommerce 6.0 or higher

== Privacy ==

BK Signals for WooCommerce may use a first-party visitor cookie named `bksignals_vid` to count live visitors, product views, and active cart activity.

The plugin may store product IDs, anonymous visitor identifiers, product activity timestamps, active cart quantities, and WooCommerce order activity used for sales counters and recent sales notifications.

Recent sales notifications display masked customer names and shortened product names. The plugin does not send this activity data to an external service by default.

== Shortcodes ==

You can create widgets from the BK Signals admin pages and copy their generated shortcodes.

Common shortcode examples:

`[bk_signals_live_visitors]`
`[bk_signals_views window="7"]`
`[bk_signals_add_to_cart]`
`[bk_signals_sales window="7"]`
`[bk_signals_general widget="general-1"]`

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/bk-signals-for-woocommerce/` directory, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the Plugins screen in WordPress.
3. Make sure WooCommerce is installed and active.
4. Go to BK Signals in the WordPress admin menu.
5. Create a widget, choose a design, copy the shortcode, and place it on a product page or theme template.

== Frequently Asked Questions ==

= Does BK Signals for WooCommerce require WooCommerce? =

Yes. BK Signals for WooCommerce is built for WooCommerce product activity and requires WooCommerce to be installed and active.

= Does the plugin count admin activity? =

Admin product page views are excluded by default. A setting is available for testing if you want to count admin views temporarily.

= Does Active Carts mean historical add-to-cart clicks? =

No. Active Carts is designed to show current in-cart demand. If a visitor removes a product from the cart, the active cart count can decrease.

= Are recent sales popups fake or generated? =

No. Recent Sales Popup uses real WooCommerce order activity and masks customer names for privacy.

= Can I customize shortcodes? =

Yes. Widget pages include editable shortcode keys and copy buttons.

== Changelog ==

= 1.0.0 =
* Initial release with Live Visitors, Views, Active Carts, Sales, Recent Sales Popup, General Widget, widget editor, design previews, WordPress dashboard summary, privacy policy text, and WooCommerce HPOS compatibility.

== Upgrade Notice ==

= 1.0.0 =
Initial public release.
