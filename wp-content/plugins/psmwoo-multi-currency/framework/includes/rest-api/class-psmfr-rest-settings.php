<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMFR_Rest_Settings {

	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );
	}

	/**
	 * Registers the REST API routes.
	 *
	 * @return void
	 */
	public static function register_rest_routes() {
		register_rest_route(
			'psmfr/v1',
			'/settings',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_settings' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'set_settings' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'primaryColor' => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_hex_color',
							'validate_callback' => array( 'PSMFR_Helper', 'rest_validate_hex_color' ),
						),
						'linkColor'    => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_hex_color',
							'validate_callback' => array( 'PSMFR_Helper', 'rest_validate_hex_color' ),
						),
						'dangerColor'  => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_hex_color',
							'validate_callback' => array( 'PSMFR_Helper', 'rest_validate_hex_color' ),
						),
						'successColor' => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_hex_color',
							'validate_callback' => array( 'PSMFR_Helper', 'rest_validate_hex_color' ),
						),
						'warningColor' => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_hex_color',
							'validate_callback' => array( 'PSMFR_Helper', 'rest_validate_hex_color' ),
						),
						'popupZIndex'  => array(
							'required'          => true,
							'validate_callback' => array( 'PSMFR_Helper', 'rest_validate_positive_integer' ),
						),
					),
				),
			)
		);

		// reset settings.
		register_rest_route(
			'psmfr/v1',
			'/settings/reset',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'reset_settings' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			)
		);
	}

	/**
	 * Retrieves the settings.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_settings( $request ) {
		$settings = get_option( 'psmfr_settings', array() );
		return rest_ensure_response( $settings );
	}

	/**
	 * Sets the settings.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function set_settings( $request ) {

		$settings = array(
			'primaryColor' => $request->get_param( 'primaryColor' ),
			'linkColor'    => $request->get_param( 'linkColor' ),
			'dangerColor'  => $request->get_param( 'dangerColor' ),
			'successColor' => $request->get_param( 'successColor' ),
			'warningColor' => $request->get_param( 'warningColor' ),
			'popupZIndex'  => $request->get_param( 'popupZIndex' ),
		);
		update_option( 'psmfr_settings', $settings );
		return rest_ensure_response( $settings );
	}

	/**
	 * Resets the settings.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function reset_settings( $request ) {

		$settings = PSMFR_Installation::reset_psmfr_settings();
		return rest_ensure_response( $settings );
	}
}

PSMFR_Rest_Settings::init();
