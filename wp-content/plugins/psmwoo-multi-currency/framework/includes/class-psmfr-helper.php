<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMFR_Helper {

	/**
	 * Validate callback for numeric parameter in rest api.
	 *
	 * @param mixed           $param - Parameter value.
	 * @param WP_REST_Request $request - Request object.
	 * @param string          $key - Parameter key.
	 * @return boolean
	 */
	public static function rest_validate_positive_integer( $param, $request, $key ) {
		$param = absint( $param );
		if ( $param ) {
			$request->set_param( $key, $param );
		}
		return $param > 0;
	}

	/**
	 * Validate callback for binary parameter in rest api.
	 *
	 * @param mixed           $param - Parameter value.
	 * @param WP_REST_Request $request - Request object.
	 * @param string          $key - Parameter key.
	 * @return boolean
	 */
	public static function rest_validate_binary_integer( $param, $request, $key ) {
		$param = absint( $param );
		if ( $param < 2 ) {
			$request->set_param( $key, $param );
			return true;
		}
		return false;
	}

	/**
	 * Validate callback for boolean parameter in rest api.
	 *
	 * @param mixed           $param - Parameter value.
	 * @param WP_REST_Request $request - Request object.
	 * @param string          $key - Parameter key.
	 * @return boolean
	 */
	public static function rest_validate_boolean( $param, $request, $key ) {
		if ( is_bool( $param ) === true ) {
			$request->set_param( $key, $param );
			return true;
		}
		return false;
	}

	/**
	 * Validate callback for boolean parameter in rest api.
	 *
	 * @param mixed           $param - Parameter value.
	 * @param WP_REST_Request $request - Request object.
	 * @param string          $key - Parameter key.
	 * @return boolean
	 */
	public static function rest_validate_non_empty_string( $param, $request, $key ) {
		$param = sanitize_text_field( $param );
		$request->set_param( $key, $param );
		return ! empty( $param );
	}

	/**
	 * Validate callback for email parameter in rest api.
	 *
	 * @param mixed           $param - Parameter value.
	 * @param WP_REST_Request $request - Request object.
	 * @param string          $key - Parameter key.
	 * @return boolean
	 */
	public static function rest_validate_email( $param, $request, $key ) {
		$param = sanitize_email( $param );
		if ( is_email( $param ) ) {
			$request->set_param( $key, $param );
			return true;
		}
		return false;
	}

	/**
	 * Validate callback for order parameter in rest api.
	 *
	 * @param mixed           $param - Parameter value.
	 * @param WP_REST_Request $request - Request object.
	 * @param string          $key - Parameter key.
	 * @return boolean
	 */
	public static function rest_validate_sort_order( $param, $request, $key ) {
		$allowed = array( 'asc', 'desc' );
		$param = strtolower( sanitize_text_field( $param ) );
		if ( in_array( $param, $allowed ) ) {
			$request->set_param( $key, $param );
			return true;
		}
		return false;
	}

	/**
	 * Validate callback for hex color parameter in rest api.
	 *
	 * @param mixed           $param - Parameter value.
	 * @param WP_REST_Request $request - Request object.
	 * @param string          $key - Parameter key.
	 * @return boolean
	 */
	public static function rest_validate_hex_color( $param, $request, $key ) {
		return preg_match( '/^#([a-f0-9]{3}){1,2}$/i', $param ) === 1;
	}

	/**
	 * Validate callback for hex color parameter in rest api.
	 *
	 * @param mixed           $param - Parameter value.
	 * @param WP_REST_Request $request - Request object.
	 * @param string          $key - Parameter key.
	 * @return boolean
	 */
	public static function rest_validate_datetime( $param, $request, $key ) {
		return (bool) strtotime( $param ) && (bool) preg_match( '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $param );
	}

	/**
	 * Renders JS global level object 'psmfr' for the psm plugins.
	 *
	 * @return void
	 */
	public static function render_framework_js_object() {

		// global js object.
		$psmfr = array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'restURL'       => untrailingslashit( get_rest_url() ),
			'nonce'         => wp_create_nonce( 'wp_rest' ),
			'isRTL'         => is_rtl(),
			'locale'        => get_user_locale(),
			'isProductPage' => is_product(),
			'isShop'        => is_shop(),
			'isCart'        => is_cart(),
			'isCheckout'    => is_checkout(),
			'isAccountPage' => is_account_page(),
			'isAdmin'       => is_admin(),
			'currentPage'   => is_admin() ? ( get_current_screen() ? get_current_screen()->id : '' ) : ( is_singular() ? get_queried_object_id() : '' ),
		);
		if ( ! is_admin() ) { // frontend only.
			$current_user = wp_get_current_user();

			$customer = $current_user->ID ? new WC_Customer( $current_user->ID ) : WC()->customer;
			$billing = array(
				'firstName'    => $customer->get_billing_first_name(),
				'lastName'     => $customer->get_billing_last_name(),
				'country'      => $customer->get_billing_country(),
				'company'      => $customer->get_billing_company(),
				'addressLine1' => $customer->get_billing_address_1(),
				'addressLine2' => $customer->get_billing_address_2(),
				'city'         => $customer->get_billing_city(),
				'postcode'     => $customer->get_billing_postcode(),
				'state'        => $customer->get_billing_state(),
				'phone'        => $customer->get_billing_phone(),
				'email'        => $customer->get_billing_email(),
			);
			if ( ! $billing['country'] ) {
				// get country and state from wooommerce store address.
				$billing['country'] = WC()->countries->get_base_country();
				$billing['state']   = WC()->countries->get_base_state();
			}
			$shipping = array(
				'firstName'    => $customer->get_shipping_first_name(),
				'lastName'     => $customer->get_shipping_last_name(),
				'country'      => $customer->get_shipping_country(),
				'company'      => $customer->get_shipping_company(),
				'addressLine1' => $customer->get_shipping_address_1(),
				'addressLine2' => $customer->get_shipping_address_2(),
				'city'         => $customer->get_shipping_city(),
				'postcode'     => $customer->get_shipping_postcode(),
				'state'        => $customer->get_shipping_state(),
				'phone'        => $customer->get_shipping_phone(),
			);
			if ( ! $shipping['country'] ) {
				// get country and state from wooommerce store address.
				$shipping['country'] = WC()->countries->get_base_country();
				$shipping['state']   = WC()->countries->get_base_state();
			}
			$psmfr['currentUser'] = array(
				'isLoggedIn'      => is_user_logged_in(),
				'role'            => $current_user->roles ? $current_user->roles[0] : '',
				'billingAddress'  => $billing,
				'shippingAddress' => $shipping,
			);
		}
		$psmfr = apply_filters( 'psmfr_js_object', $psmfr );
		wp_add_inline_script(
			'jquery-core',
			'var psmfr = ' . wp_json_encode( $psmfr ) . ';',
			'before'
		);

		// global css.
		$settings = get_option( 'psmfr_settings', array() );
		$css = ':root {'
			. '--psmfr-primary-color: ' . ( ! is_admin() && ! empty( $settings['primaryColor'] ) ? esc_attr( $settings['primaryColor'] ) : '#0a2540' ) . ';'
			. '--psmfr-link-color: ' . ( ! is_admin() && ! empty( $settings['linkColor'] ) ? esc_attr( $settings['linkColor'] ) : '#2271B1' ) . ';'
			. '--psmfr-danger-color: ' . ( ! is_admin() && ! empty( $settings['dangerColor'] ) ? esc_attr( $settings['dangerColor'] ) : '#CF1322' ) . ';'
			. '--psmfr-success-color: ' . ( ! is_admin() && ! empty( $settings['successColor'] ) ? esc_attr( $settings['successColor'] ) : '#389E0D' ) . ';'
			. '--psmfr-warning-color: ' . ( ! is_admin() && ! empty( $settings['warningColor'] ) ? esc_attr( $settings['warningColor'] ) : '#D48806' ) . ';'
			. '--psmfr-popup-z-index: ' . ( ! empty( $settings['popupZIndex'] ) ? esc_attr( $settings['popupZIndex'] ) : '9999' ) . ';'
			. '}';

		wp_add_inline_style( 'psmfr', $css );
	}

	/**
	 * Get the page ID for the current language of given page ID.
	 * This is applicable if multilangual plugins like WPML or Polylang are used.
	 * We save parent page ID in the settings and use it to get the current language page ID.
	 *
	 * @param int $page_id - The page ID to get the current language page ID for.
	 * @return int The current language page ID.
	 */
	public static function get_current_lang_page_id( $page_id ) {

		if ( ! $page_id || ! is_numeric( $page_id ) ) {
			return 0;
		}

		// WPML support.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) && function_exists( 'icl_object_id' ) ) {
			$current_lang_page_id = apply_filters( 'wpml_object_id', $page_id, 'page', true ); //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML core hook, do not prefix
			return $current_lang_page_id ? (int) $current_lang_page_id : (int) $page_id;
		}

		// Polylang support.
		if ( function_exists( 'pll_get_post' ) ) {
			$current_lang_page_id = pll_get_post( $page_id );
			return $current_lang_page_id ? (int) $current_lang_page_id : (int) $page_id;
		}
		return (int) $page_id;
	}
}
