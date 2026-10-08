<?php
/**
 * Plugin Name: BK Signals for WooCommerce
 * Description: Add social proof and product activity data to WooCommerce product pages to help improve conversion rates.
 * Version:     1.0.0
 * Author:      Bermikod Plugins
 * Author URI:  https://bermikodplugins.com
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bk-signals-for-woocommerce
 * Domain Path: /languages
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 6.0
 * WC tested up to:      10.6
 */

defined( 'ABSPATH' ) || exit;

define( 'BKSIGNALS_VERSION',   '1.0.0' );
define( 'BKSIGNALS_FILE',      __FILE__ );
define( 'BKSIGNALS_DIR',       plugin_dir_path( __FILE__ ) );
define( 'BKSIGNALS_URL',       plugin_dir_url( __FILE__ ) );
define( 'BKSIGNALS_BASENAME',  plugin_basename( __FILE__ ) );

// HPOS.
function bksignals_declare_woocommerce_features() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
}
add_action( 'before_woocommerce_init', 'bksignals_declare_woocommerce_features' );

// WooCommerce.
function bksignals_check_woocommerce() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>'
                . esc_html__( 'BK Signals for WooCommerce requires WooCommerce to be installed and active.', 'bk-signals-for-woocommerce' )
                . '</p></div>';
        } );
        return false;
    }
    return true;
}

// Privacy.
function bksignals_add_privacy_policy_content() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
        return;
    }

    $content = wp_kses_post(
        wpautop(
            __( 'BK Signals for WooCommerce may use a first-party visitor cookie named bksignals_vid to count live visitors, product views, and active cart activity. BK Signals for WooCommerce may store product IDs, anonymous visitor identifiers, product activity timestamps, active cart quantities, and WooCommerce order activity used for sales counters and recent sales notifications. Recent sales notifications display masked customer names and shortened product names. The plugin does not send this activity data to an external service by default.', 'bk-signals-for-woocommerce' )
        )
    );

    wp_add_privacy_policy_content( 'BK Signals for WooCommerce', $content );
}
add_action( 'admin_init', 'bksignals_add_privacy_policy_content' );

// Init.
function bksignals_init() {
    if ( ! bksignals_check_woocommerce() ) {
        return;
    }

    require_once BKSIGNALS_DIR . 'core/class-installer.php';
    require_once BKSIGNALS_DIR . 'core/class-database.php';
    require_once BKSIGNALS_DIR . 'core/class-helpers.php';
    require_once BKSIGNALS_DIR . 'core/class-tracker.php';
    require_once BKSIGNALS_DIR . 'core/class-cron.php';
    require_once BKSIGNALS_DIR . 'core/class-assets.php';
    require_once BKSIGNALS_DIR . 'core/class-ajax.php';
    require_once BKSIGNALS_DIR . 'core/class-shortcodes.php';

    BKSignals_Installer::maybe_upgrade();

    if ( is_admin() ) {
        require_once BKSIGNALS_DIR . 'admin/class-dashboard.php';
        require_once BKSIGNALS_DIR . 'admin/class-live-visitors.php';
        require_once BKSIGNALS_DIR . 'admin/class-views.php';
        require_once BKSIGNALS_DIR . 'admin/class-cart.php';
        require_once BKSIGNALS_DIR . 'admin/class-sales.php';
        require_once BKSIGNALS_DIR . 'admin/class-popup.php';
        require_once BKSIGNALS_DIR . 'admin/class-general-widget.php';
        require_once BKSIGNALS_DIR . 'admin/class-settings.php';

        BKSignals_Dashboard::init();
        BKSignals_Admin_Live_Visitors::init();
        BKSignals_Admin_Views::init();
        BKSignals_Admin_Cart::init();
        BKSignals_Admin_Sales::init();
        BKSignals_Admin_Popup::init();
        BKSignals_Admin_General_Widget::init();
        BKSignals_Admin_Settings::init();
    }

    BKSignals_Tracker::init();
    BKSignals_Cron::init();
    BKSignals_Assets::init();
    BKSignals_Ajax::init();
    BKSignals_Shortcodes::init();

}
add_action( 'plugins_loaded', 'bksignals_init' );

register_activation_hook( __FILE__, function() {
    $dir = plugin_dir_path( __FILE__ );
    require_once $dir . 'core/class-installer.php';
    require_once $dir . 'core/class-cron.php';
    BKSignals_Installer::activate();
} );

register_deactivation_hook( __FILE__, function() {
    $dir = plugin_dir_path( __FILE__ );
    require_once $dir . 'core/class-installer.php';
    require_once $dir . 'core/class-cron.php';
    BKSignals_Installer::deactivate();
} );
