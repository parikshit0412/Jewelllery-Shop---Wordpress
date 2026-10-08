<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Checkout_Currency {

	/**
	 * Checkout currency.
	 *
	 * @var string
	 */
	public static $currency = '';

	/**
	 * All possible currencies for the checkout.
	 *
	 * @var array
	 */
	public static $currencies = array();

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'set_currencies' ), 1 );
		add_action( 'init', array( __CLASS__, 'set_current_currency' ), 3 );
		add_filter( 'woocommerce_available_payment_gateways', array( __CLASS__, 'filter_payment_gateways' ) );

		if ( is_admin() ) {
			add_action( 'wp_ajax_psmmc_force_by_address_country', array( __CLASS__, 'force_by_address_country' ) );
			add_action( 'wp_ajax_nopriv_psmmc_force_by_address_country', array( __CLASS__, 'force_by_address_country' ) );
			add_action( 'wp_ajax_psmmc_get_checkout_data', array( __CLASS__, 'get_checkout_data' ) );
			add_action( 'wp_ajax_nopriv_psmmc_get_checkout_data', array( __CLASS__, 'get_checkout_data' ) );
		} else {
			// classic checkout order update review.
			add_action( 'woocommerce_checkout_update_order_review', array( __CLASS__, 'classic_update_order_review' ) );
		}
	}

	/**
	 * Set the checkout currencies.
	 *
	 * @return void
	 */
	public static function set_currencies() {

		$currencies = get_option( 'psmwoo_mc_checkout_currencies' );
		foreach ( $currencies as $code => $currency ) {
			if ( ! $currency['enabled'] ) {
				unset( $currencies[ $code ] );
			}
		}
		self::$currencies = $currencies;
	}

	/**
	 * Set the current currency for checkout page.
	 *
	 * @return void
	 */
	public static function set_current_currency() {

		$currency = '';

		// checkout in different currency.
		$checkout_options = get_option( 'psmwoo_mc_checkout_options' );
		if ( $checkout_options['checkoutDifferentCurrency'] && WC()->customer ) {

			// force payment by specific currency is disabled.
			$currency = PSMWOO_MC_Switcher::$currency;

			if ( ! array_key_exists( $currency, self::$currencies ) ) {
				$currency = get_option( 'woocommerce_currency' );
			}
		} else {
			// checkout in different currency is disabled.
			$currency = PSMWOO_MC_Switcher::$currency;
		}

		self::$currency = $currency;
	}

	/**
	 * Filter the payment gateways for the checkout page depending on the current currency.
	 *
	 * @param array $gateways Payment gateways.
	 * @return array
	 */
	public static function filter_payment_gateways( $gateways ) {

		global $wp;

		if ( PSMWOO_MC_Helper::is_checkout() || (
			isset( $wp->query_vars['rest_route'] ) &&
			preg_match( '/^\/wc\/store\/v1/', $wp->query_vars['rest_route'] )
		) ) {

			$currency = self::$currencies[ self::$currency ];

			// filter payment methods for its existance.
			$payment_methods = array();
			if ( ! empty( $currency['paymentMethods'] ) ) {
				foreach ( $currency['paymentMethods'] as $method ) {
					if ( array_key_exists( $method, $gateways ) ) {
						$payment_methods[] = $method;
					}
				}
			}

			if ( empty( $payment_methods ) ) {
				return $gateways;
			}

			return array_intersect_key( $gateways, array_flip( $payment_methods ) );
		}

		return $gateways;
	}

	/**
	 * Classic checkout order review.
	 *
	 * @param array $data The data.
	 * @return void
	 */
	public static function classic_update_order_review( $data ) {

		parse_str( $data, $parsed_data );
		if ( ! isset( $parsed_data['billing_country'] ) || ! isset( $parsed_data['shipping_country'] ) ) {
			return;
		}

		$billing_country = isset( $parsed_data['billing_country'] ) ? sanitize_text_field( wp_unslash( $parsed_data['billing_country'] ) ) : '';
		$shipping_country = isset( $parsed_data['shipping_country'] ) ? sanitize_text_field( wp_unslash( $parsed_data['shipping_country'] ) ) : '';
		$ship_to_different_address = isset( $parsed_data['ship_to_different_address'] ) ? intval( $parsed_data['ship_to_different_address'] ) : 0;
		if ( ! $ship_to_different_address ) {
			$shipping_country = $billing_country;
		}

		WC()->customer->set_props(
			array(
				'billing_country'  => $billing_country,
				'shipping_country' => $shipping_country,
			)
		);

		self::set_current_currency();
	}

	/**
	 * Force the currency by the address country.
	 *
	 * @return void
	 */
	public static function force_by_address_country() {

		if ( ! check_ajax_referer( 'psmmc_force_by_address_country', 'nounce', false ) ) {
			wp_send_json_error( 'Invalid security token.' );
		}

		$shipping_country = isset( $_POST['shippingCountry'] ) ? sanitize_text_field( wp_unslash( $_POST['shippingCountry'] ) ) : '';
		$billing_country = isset( $_POST['billingCountry'] ) ? sanitize_text_field( wp_unslash( $_POST['billingCountry'] ) ) : '';
		if ( empty( $shipping_country ) || empty( $billing_country ) ) {
			wp_send_json_error( 'Invalid country.' );
		}

		WC()->customer->set_props(
			array(
				'billing_country'  => $billing_country,
				'shipping_country' => $shipping_country,
			)
		);

		self::set_current_currency();
		wp_send_json_success( self::get_js_data() );
	}

	/**
	 * Get the checkout data.
	 *
	 * @return void
	 */
	public static function get_checkout_data() {

		if ( ! check_ajax_referer( 'psmmc_force_by_address_country', 'nounce', false ) ) {
			wp_send_json_error( 'Invalid security token.' );
		}

		wp_send_json_success( self::get_js_data() );
	}

	/**
	 * Get the JS data for the checkout page.
	 *
	 * @return array
	 */
	public static function get_js_data() {

		$notice = '';
		$checkout_options = get_option( 'psmwoo_mc_checkout_options' );
		if ( $checkout_options['enableAddressCountryNotice'] && PSMWOO_MC_Switcher::$currency !== self::$currency ) {

			$notice = PSMWOO_MC_Translations::get( 'psmmc-address-country-notice', nl2br( $checkout_options['addressCountryNotice'] ) );
			$notice = str_replace( '%currency-selected%', PSMWOO_MC_Switcher::$currency, $notice );
			$notice = str_replace( '%checkout-currency%', self::$currency, $notice );
		}

		$data = array(
			'ajax_url'                      => admin_url( 'admin-ajax.php' ),
			'nounce'                        => wp_create_nonce( 'psmmc_force_by_address_country' ),
			'currency'                      => PSMWOO_MC_Switcher::$currency,
			'checkoutCurrency'              => self::$currency,
			'notice'                        => $notice,
			'forceByAddressCountry'         => false,
			'addressCountryMethod'          => 'billing',
			'reloadPageAfterCurrencyChange' => false,
		);

		return $data;
	}
}

PSMWOO_MC_Checkout_Currency::init();
