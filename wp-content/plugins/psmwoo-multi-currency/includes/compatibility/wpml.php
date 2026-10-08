<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Compatibility with WPML plugin.
 *
 * @since 1.0.0
 */
class PSMWOO_MC_WPML {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {

		add_action( 'admin_init', array( __CLASS__, 'register_strings' ) );
		add_filter( 'psmwoo_mc_is_checkout', array( __CLASS__, 'is_checkout' ) );
	}

	/**
	 * Register custom strings for translation
	 */
	public static function register_strings() {

		$strings = get_option( 'psmmc-string-translation', array() );

		// latest compatible.
		if ( defined( 'WPML_PLUGIN_PATH' ) ) {
			foreach ( $strings as $name => $str ) {
				do_action( 'wpml_register_single_string', 'psmwoo-multi-currency', $name, $str ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML core hook, cannot be prefixed.
			}
			return;
		}
	}

	/**
	 * Check if the current page is checkout.
	 *
	 * @return bool
	 */
	public static function is_checkout() {
		if ( function_exists( 'icl_object_id' ) && function_exists( 'wc_get_page_id' ) ) {
			$checkout_page_id = wc_get_page_id( 'checkout' );
			if ( $checkout_page_id && icl_object_id( $checkout_page_id, 'page', true ) === get_the_ID() ) {
				return true;
			}
		}

		return false;
	}
}

PSMWOO_MC_WPML::init();
