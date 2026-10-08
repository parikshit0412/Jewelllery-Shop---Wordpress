<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Compatibility with caching plugins.
 *
 * @since 1.0.0
 */
class PSMMC_Ajax {

	/**
	 * Initialize the class.
	 */
	public static function init() {

		add_action( 'wp_ajax_psmmc_get_current_currency', array( __CLASS__, 'get_current_currency_callback' ) );
		add_action( 'wp_ajax_nopriv_psmmc_get_current_currency', array( __CLASS__, 'get_current_currency_callback' ) );
	}

	/**
	 * AJAX callback to get current currency.
	 *
	 * @return void
	 */
	public static function get_current_currency_callback() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wp_rest' ) ) {
			wp_send_json_error( array( 'message' => 'Invalid request.' ) );
		}

		$current_currency = PSMWOO_MC_Switcher::$currency;
		wp_send_json_success( array( 'currency' => $current_currency ) );
	}
}

PSMMC_Ajax::init();
