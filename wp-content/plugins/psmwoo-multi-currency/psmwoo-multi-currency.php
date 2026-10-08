<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name: PSM Multi-Currency for WooCommerce
 * Plugin URI: https://psmplugins.com/multi-currency-for-woocommerce/
 * Description: This plugin enhances your WooCommerce store by adding a multi-currency switcher, making it ideal for businesses with a global customer base. It allows customers to easily switch between currencies, improving their shopping experience and supporting international sales.
 * Version: 1.0.4
 * Author: PSM Plugins
 * Author URI: https://psmplugins.com
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: psmwoo-multi-currency
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 *
 * @package psmwoo-multi-currency
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do not load plugin files if pro is active.
if ( class_exists( 'PSMWOO_Multi_Currency_Pro' ) ) {
	return;
}

final class PSMWOO_Multi_Currency {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public static $version = '1.0.4';

	/**
	 * The single instance of the class.
	 *
	 * @var PSMWOO_Multi_Currency
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'includes' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( __CLASS__, 'add_plugin_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'add_plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Include the plugin files.
	 */
	public static function includes() {

		// return if woocommerce is not installed.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// define constants.
		self::define_constants();

		// load framework if not already loaded.
		if ( ! defined( 'PSMFR_LOADED' ) ) {
			require_once PSMWOO_MC_ABSPATH . 'framework/class-psmfr.php';
			PSMFR::init( PSMWOO_MC_ABSPATH, PSMWOO_MC_PLUGIN_URL, 'psmwmc' );
			define( 'PSMFR_LOADED', true );
		}

		// load plugin files.
		require_once PSMWOO_MC_ABSPATH . 'includes/autoloader.php';

		// deactivation hook.
		register_deactivation_hook( __FILE__, array( 'PSMWOO_MC_Installation', 'deactivate' ) );
	}

	/**
	 * Defines global constants that can be availabel anywhere in WordPress
	 *
	 * @return void
	 */
	public static function define_constants() {
		self::define( 'PSMWOO_MC_PLUGIN_FILE', __FILE__ );
		self::define( 'PSMWOO_MC_STORE_URL', 'https://psmplugins.com' );
		self::define( 'PSMWOO_MC_ABSPATH', plugin_dir_path( __FILE__ ) );
		self::define( 'PSMWOO_MC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		self::define( 'PSMWOO_MC_VERSION', self::$version );
	}

	/**
	 * Define constants
	 *
	 * @param string $name - name of global constant.
	 * @param string $value - value of constant.
	 * @return void
	 */
	private static function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Dynamic constant name, validated by define_constants().
		}
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links - array of plugin action links.
	 * @return array
	 */
	public static function add_plugin_links( $links ) {
		$custom_links = array(
			'<a href="' . admin_url( 'admin.php?page=psmwoo-multi-currency-settings' ) . '">' . __( 'Settings', 'psmwoo-multi-currency' ) . '</a>',
		);
		return array_merge( $links, $custom_links );
	}

	/**
	 * Add plugin row meta links
	 *
	 * @param array  $links - array of plugin row meta links.
	 * @param string $file - plugin file path.
	 * @return array
	 */
	public static function add_plugin_row_meta( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$custom_links = array(
				'<a href="https://psmplugins.com/documentation">' . __( 'Documentation', 'psmwoo-multi-currency' ) . '</a>',
				'<a href="https://psmplugins.com/support">' . __( 'Support', 'psmwoo-multi-currency' ) . '</a>',
			);
			$links = array_merge( $links, $custom_links );
		}
		return $links;
	}
}

PSMWOO_Multi_Currency::init();
