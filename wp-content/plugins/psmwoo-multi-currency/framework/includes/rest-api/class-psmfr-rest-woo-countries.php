<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMFR_Rest_Woo_Countries {

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
			'/woo-countries',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_woo_countries' ),
				// This endpoint is intentionally public. Only public timezone data is returned.
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'psmfr/v1',
			'/woo-states',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_woo_states' ),
				// This endpoint is intentionally public. Only public timezone data is returned.
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Returns WooCommerce countries from WC()->countries.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array
	 */
	public static function get_woo_countries( $request ) {

		if ( ! class_exists( 'WC_Countries' ) ) {
			return array();
		}

		$wc_countries = new WC_Countries();
		$countries = $wc_countries->get_countries();
		$result = array();
		foreach ( $countries as $code => $name ) {
			$result[] = array(
				'value' => $code,
				'label' => $name,
			);
		}
		return $result;
	}

	/**
	 * Returns WooCommerce states for a given country.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array
	 */
	public static function get_woo_states( $request ) {

		if ( ! class_exists( 'WC_Countries' ) ) {
			return array();
		}

		$country = isset( $request['country'] ) ? sanitize_text_field( $request['country'] ) : '';
		if ( empty( $country ) ) {
			return new WP_Error( 'missing_country', 'Country parameter is required.', array( 'status' => 400 ) );
		}

		$wc_countries = new WC_Countries();
		$states = $wc_countries->get_states( $country );
		$result = array();
		if ( is_array( $states ) ) {
			foreach ( $states as $code => $name ) {
				$result[] = array(
					'value' => $code,
					'label' => $name,
				);
			}
		}
		return $result;
	}
}

PSMFR_Rest_Woo_Countries::init();
