<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Settings REST API class.
 *
 * @package psmwoo-multi-currency
 * @since   1.0.0
 */
class PSMWOO_MC_Rest_Switcher {

	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public static function register_rest_routes() {

		register_rest_route(
			'psmmc/v1',
			'/switcher/switch-currency',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'switch_currency' ),
				// This endpoint is intentionally public. It is safe for public access as it only sets a currency cookie and does not expose sensitive data.
				'permission_callback' => '__return_true',
				'args'                => array(
					'code' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							$currency_codes = get_woocommerce_currencies();
							$currenies = get_option( 'psmwoo_mc_currencies' );
							if ( ! ( array_key_exists( $param, $currency_codes ) || array_key_exists( $param, $currenies ) ) ) {
								return new WP_Error( 'rest_invalid_param', __( 'Invalid currency code.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
							}
							return true;
						},
					),
				),
			)
		);
	}

	/**
	 * Switch currency.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function switch_currency( $request ) {

		$currency_code = $request->get_param( 'code' );
		PSMWOO_MC_Helper::set_cookie_currency( $currency_code );

		// change product transient value, so that prices will be updated in the cache.
		delete_transient( 'product-transient-version' );

		do_action( 'psmwoo_mc_switch_currency', $currency_code );

		return new WP_REST_Response( array( 'success' => true ), 200 );
	}
}

PSMWOO_MC_Rest_Switcher::init();
