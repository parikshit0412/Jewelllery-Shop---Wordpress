<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMFR {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public static $version = '1.0.0';

	/**
	 * Initialize the class.
	 *
	 * @param string $abspath - Absolute path of the plugin.
	 * @param string $plugin_url - URL of the plugin.
	 * @param string $textdomain - Text domain of the plugin.
	 * @return void
	 */
	public static function init( $abspath, $plugin_url, $textdomain = '' ) {

		// define constants.
		self::define_constants( $abspath, $plugin_url, $textdomain );

		// load installation functions.
		require_once PSMFR_ABSPATH . 'includes/class-psmfr-installation.php';

		// load helper functions.
		require_once PSMFR_ABSPATH . 'includes/class-psmfr-helper.php';

		// load rest api functionality.
		if ( PSMFR_REST_REQUEST ) {
			foreach ( glob( PSMFR_ABSPATH . 'includes/rest-api/*.php' ) as $filename ) {
				include_once $filename;
			}
		}

		// load admin user interface.
		if ( is_admin() && ! ( wp_doing_ajax() || PSMFR_REST_REQUEST ) ) {
			require_once PSMFR_ABSPATH . 'includes/class-psmfr-admin.php';
			self::define( 'PSMFR_ADMIN_INTERFACE', true );
		} else {
			self::define( 'PSMFR_ADMIN_INTERFACE', false );
		}

		// load frontend user interface.
		if ( ! ( is_admin() || defined( 'DOING_CRON' ) || PSMFR_REST_REQUEST ) ) {
			require_once PSMFR_ABSPATH . 'includes/class-psmfr-frontend.php';
			self::define( 'PSMFR_FRONTEND_INTERFACE', true );
		} else {
			self::define( 'PSMFR_FRONTEND_INTERFACE', false );
		}
	}

	/**
	 * Define constants.
	 *
	 * @param string $abspath - Absolute path of the plugin.
	 * @param string $plugin_url - URL of the plugin.
	 * @param string $textdomain - Text domain of the plugin.
	 * @return void
	 */
	public static function define_constants( $abspath, $plugin_url, $textdomain ) {

		self::define( 'PSMFR_PLUGIN_ABSPATH', $abspath );
		self::define( 'PSMFR_PLUGIN_URL', $plugin_url );
		self::define( 'PSMFR_ABSPATH', $abspath . 'framework/' );
		self::define( 'PSMFR_VERSION', self::$version );
		self::define( 'PSMFR_TEXTDOMAIN', $textdomain );
		self::define( 'PSMFR_STORE_URL', 'https://psmplugins.com' );

		$is_rest_request = isset( $_SERVER['REQUEST_URI'] ) && strpos( wp_unslash( $_SERVER['REQUEST_URI'] ), '/wp-json/' ) !== false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		self::define( 'PSMFR_REST_REQUEST', $is_rest_request );
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
}
