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
class PSMWOO_MC_Rest_Settings {

	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );
	}

	/**
	 * Registers the REST API routes for the plugin.
	 *
	 * This method is called during the rest_api_init action and is responsible for
	 * registering the REST API routes for the plugin.
	 *
	 * @return void
	 */
	public static function register_rest_routes() {

		register_rest_route(
			'psmmc/v1',
			'/settings/currencies',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'list_currencies' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'add_new_currency' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'code' => array(
							'validate_callback' => function ( $param, $request, $key ) {
								$currency_codes = get_woocommerce_currencies();
								if ( ! array_key_exists( $param, $currency_codes ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid currency code.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								$currenies = get_option( 'psmwoo_mc_currencies' );
								if ( array_key_exists( $param, $currenies ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Currency code already exists.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
						),
					),
				),
			),
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/currencies/(?P<code>[a-zA-Z]+)',
			array(
				array(
					'methods'             => 'PUT',
					'callback'            => array( __CLASS__, 'update_currency' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'code'              => array(
							'validate_callback' => function ( $param, $request, $key ) {
								$currenies = get_option( 'psmwoo_mc_currencies' );
								if ( ! array_key_exists( $param, $currenies ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Currency not set.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								$store_currency_code = get_option( 'woocommerce_currency' );
								if ( $param === $store_currency_code ) {
									return new WP_Error( 'rest_invalid_param', __( 'Cannot update store currency.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
						),
						'enabled'           => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! is_bool( $param ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid enabled value.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
						),
						'currencyPosition'  => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! in_array( $param, array( 'left', 'right', 'left_space', 'right_space' ) ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid currency position.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
						),
						'thousandSeparator' => array(
							'sanitize_callback' => 'sanitize_text_field',
						),
						'decimalSeparator'  => array(
							'sanitize_callback' => 'sanitize_text_field',
						),
						'decimal'           => array(
							'sanitize_callback' => 'absint',
						),
						'rounding'          => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! in_array( $param, array( 'disabled', 'up', 'down', 'nearest' ) ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid rounding.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
						),
						'roundingTo'        => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! in_array( $param, array( 0.01, 0.1, 1, 10, 100, 1000 ) ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid rounding to.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
							'sanitize_callback' => function ( $param, $request, $key ) {
								return floatval( $param );
							},
						),
						'roundingMinus'     => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! is_numeric( $param ) || $param < 0 ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid rounding minus.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
							'sanitize_callback' => function ( $param, $request, $key ) {
								return floatval( $param );
							},
						),
						'rate'              => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! is_numeric( $param ) || $param < 0 ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid rounding minus.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
							'sanitize_callback' => function ( $param, $request, $key ) {
								return floatval( $param );
							},
						),
						'rateMode'          => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! in_array( $param, array( 'auto', 'manual' ) ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid rate mode.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
						),
						'fee'               => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! is_numeric( $param ) || $param < 0 ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid fee.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
							'sanitize_callback' => function ( $param, $request, $key ) {
								return floatval( $param );
							},
						),
						'feeMode'           => array(
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! in_array( $param, array( 'fixed', 'percentage' ) ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid fee mode.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
						),
					),
				),
				array(
					'methods'             => 'DELETE',
					'callback'            => array( __CLASS__, 'delete_currency' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'code' => array(
							'validate_callback' => function ( $param, $request, $key ) {
								$currenies = get_option( 'psmwoo_mc_currencies' );
								if ( ! array_key_exists( $param, $currenies ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Currency not set.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								$store_currency_code = get_option( 'woocommerce_currency' );
								if ( $param === $store_currency_code ) {
									return new WP_Error( 'rest_invalid_param', __( 'Cannot delete store currency.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
								return true;
							},
						),
					),
				),
			),
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/currencies/refresh-exchange-rates',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'refresh_exchange_rates' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			),
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/currencies/reorder',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'reorder' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
				'args'                => array(
					'order' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							$currenies = get_option( 'psmwoo_mc_currencies' );
							$codes = array_keys( $currenies );
							if ( count( $param ) !== count( $codes ) ) {
								return new WP_Error( 'rest_invalid_param', __( 'Invalid order.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
							}
							foreach ( $param as $code ) {
								if ( ! in_array( $code, $codes ) ) {
									return new WP_Error( 'rest_invalid_param', __( 'Invalid order.', 'psmwoo-multi-currency' ), array( 'status' => 400 ) );
								}
							}
							return true;
						},
					),
				),
			),
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/currencies/woocommerce',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_woocommerce_currencies' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			),
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/checkout-currencies',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_checkout_currencies' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
				),
				array(
					'methods'             => 'PUT',
					'callback'            => array( __CLASS__, 'update_checkout_currencies' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'currencies' => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! is_array( $param ) ) {
									return false;
								}
								$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
								$checkout_currencies = get_option( 'psmwoo_mc_checkout_currencies' );
								foreach ( $param as $code => $currency ) {
									if ( ! array_key_exists( $code, $checkout_currencies ) ) {
										return false;
									}
									foreach ( $currency['paymentMethods'] as $method ) {
										if ( ! array_key_exists( $method, $payment_gateways ) ) {
											return false;
										}
									}
								}
								return true;
							},
						),
					),
				),
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/payment-methods',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_payment_methods' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/checkout-options',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_checkout_options' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
				),
				array(
					'methods'             => 'PUT',
					'callback'            => array( __CLASS__, 'update_checkout_options' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'checkoutDifferentCurrency'  => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_bool( $param );
							},
						),
						'enableAddressCountryNotice' => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_bool( $param );
							},
						),
						'addressCountryNotice'       => array(
							'required'          => true,
							'sanitize_callback' => 'wp_kses_post',
						),
					),
				),
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/checkout-options/reset',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'reset_checkout_options' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/display-options',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_display_options' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
				),
				array(
					'methods'             => 'PUT',
					'callback'            => array( __CLASS__, 'update_display_options' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'enableSingleProduct'    => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_bool( $param );
							},
						),
						'singleProductPosition'  => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return in_array( $param, array( 'before-short-description', 'after-short-descriptoion' ) );
							},
						),
						'switcherSize'           => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return in_array( $param, array( 'small', 'medium' ) );
							},
						),
						'switcherFlag'           => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_bool( $param );
							},
						),
						'switcherCurrencyName'   => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_bool( $param );
							},
						),
						'switcherCurrencySymbol' => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_bool( $param );
							},
						),
						'switcherCurrencyCode'   => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_bool( $param );
							},
						),
					),
				),
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/display-options/reset',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'reset_display_options' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/auto-select-country-currencies',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_auto_select_country_currencies' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
				),
				array(
					'methods'             => 'PUT',
					'callback'            => array( __CLASS__, 'update_auto_select_country_currencies' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'currencies' => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								if ( ! is_array( $param ) ) {
									return false;
								}
								$wc_countries = WC()->countries->get_countries();
								$auto_select_country_currencies = get_option( 'psmwoo_mc_auto_select_country_currencies' );
								foreach ( $param as $code => $currency ) {
									if ( ! array_key_exists( $code, $auto_select_country_currencies ) ) {
										return false;
									}
									if ( ! is_array( $currency['countries'] ) ) {
										return false;
									}
									foreach ( $currency['countries'] as $country ) {
										if ( ! is_string( $country ) ) {
											return false;
										}
										if ( ! array_key_exists( $country, $wc_countries ) ) {
											return false;
										}
									}
								}
								return true;
							},
						),
					),
				),
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/advanced',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_advanced_settings' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
				),
				array(
					'methods'             => 'PUT',
					'callback'            => array( __CLASS__, 'update_advanced_settings' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
					'args'                => array(
						'currencyExchangeRateApi' => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_string( $param );
							},
						),
						'enableCachePlugin'       => array(
							'required'          => true,
							'validate_callback' => function ( $param, $request, $key ) {
								return is_bool( $param );
							},
						),
					),
				),
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/advanced/reset',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'reset_advanced_settings' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			)
		);

		register_rest_route(
			'psmmc/v1',
			'/settings/countries',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_woocommerce_countries' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			)
		);
	}

	/**
	 * Returns the list of currencies.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function list_currencies( $request ) {

		$currencies = get_option( 'psmwoo_mc_currencies' );
		return self::get_rest_response( $currencies );
	}

	/**
	 * Adds a new currency.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function add_new_currency( $request ) {

		$code = $request->get_param( 'code' );
		$currencies = PSMWOO_MC_Helper::add_new_currency( $code );
		return self::get_rest_response( $currencies );
	}

	/**
	 * Updates a currency.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function update_currency( $request ) {

		$code = $request->get_param( 'code' );
		$currencies = get_option( 'psmwoo_mc_currencies' );
		$rate = $request->get_param( 'rateMode' ) === 'manual' ? $request->get_param( 'rate' ) : PSMWOO_MC_Helper::get_exchange_rate_from_api( get_option( 'woocommerce_currency' ), $code );

		$currency = $currencies[ $code ];
		$currency['rate'] = $rate;

		if ( is_bool( $request->get_param( 'enabled' ) ) ) {
			$currency['enabled'] = $request->get_param( 'enabled' );
		}
		if ( $request->get_param( 'currencyPosition' ) ) {
			$currency['currencyPosition'] = $request->get_param( 'currencyPosition' );
		}
		if ( $request->get_param( 'thousandSeparator' ) ) {
			$currency['thousandSeparator'] = $request->get_param( 'thousandSeparator' );
		}
		if ( $request->get_param( 'decimalSeparator' ) ) {
			$currency['decimalSeparator'] = $request->get_param( 'decimalSeparator' );
		}
		if ( $request->get_param( 'decimal' ) !== null ) {
			$currency['decimal'] = $request->get_param( 'decimal' );
		}
		if ( $request->get_param( 'rounding' ) ) {
			$currency['rounding'] = $request->get_param( 'rounding' );
		}
		if ( is_numeric( $request->get_param( 'roundingTo' ) ) && (int) $request->get_param( 'roundingTo' ) == $request->get_param( 'roundingTo' ) ) {
			$currency['roundingTo'] = (float) $request->get_param( 'roundingTo' );
		}
		if ( is_numeric( $request->get_param( 'roundingMinus' ) ) && (int) $request->get_param( 'roundingMinus' ) == $request->get_param( 'roundingMinus' ) ) {
			$currency['roundingMinus'] = (float) $request->get_param( 'roundingMinus' );
		}
		if ( $request->get_param( 'rateMode' ) ) {
			$currency['rateMode'] = $request->get_param( 'rateMode' );
		}
		if ( is_numeric( $request->get_param( 'fee' ) ) && (int) $request->get_param( 'fee' ) == $request->get_param( 'fee' ) ) {
			$currency['fee'] = (float) $request->get_param( 'fee' );
		}
		if ( $request->get_param( 'feeMode' ) ) {
			$currency['feeMode'] = $request->get_param( 'feeMode' );
		}

		$currencies[ $code ] = $currency;
		update_option( 'psmwoo_mc_currencies', $currencies );
		return self::get_rest_response( $currencies );
	}

	/**
	 * Deletes a currency.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function delete_currency( $request ) {

		$code = $request->get_param( 'code' );
		$currencies = get_option( 'psmwoo_mc_currencies' );
		unset( $currencies[ $code ] );
		update_option( 'psmwoo_mc_currencies', $currencies );

		// update checkout currencies.
		$checkout_currencies = get_option( 'psmwoo_mc_checkout_currencies' );
		unset( $checkout_currencies[ $code ] );
		update_option( 'psmwoo_mc_checkout_currencies', $checkout_currencies );

		// update auto select country currencies.
		$auto_select_country_currencies = get_option( 'psmwoo_mc_auto_select_country_currencies' );
		unset( $auto_select_country_currencies[ $code ] );
		update_option( 'psmwoo_mc_auto_select_country_currencies', $auto_select_country_currencies );

		return self::get_rest_response( $currencies );
	}

	/**
	 * Refreshes the exchange rates.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function refresh_exchange_rates( $request ) {

		PSMWOO_MC_Helper::update_exchange_rates();
		$currencies = get_option( 'psmwoo_mc_currencies' );
		return self::get_rest_response( $currencies );
	}

	/**
	 * Reorders the currencies.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function reorder( $request ) {

		$currencies = get_option( 'psmwoo_mc_currencies' );
		$order = $request->get_param( 'order' );
		$currencies = array_merge( array_flip( $order ), $currencies );
		update_option( 'psmwoo_mc_currencies', $currencies );

		// update checkout currencies.
		$checkout_currencies = get_option( 'psmwoo_mc_checkout_currencies' );
		$checkout_currencies = array_merge( array_flip( $order ), $checkout_currencies );
		update_option( 'psmwoo_mc_checkout_currencies', $checkout_currencies );

		// update auto select country currencies.
		$auto_select_country_currencies = get_option( 'psmwoo_mc_auto_select_country_currencies' );
		$auto_select_country_currencies = array_merge( array_flip( $order ), $auto_select_country_currencies );
		update_option( 'psmwoo_mc_auto_select_country_currencies', $auto_select_country_currencies );

		return self::get_rest_response( $currencies );
	}

	/**
	 * Returns the list of WooCommerce currencies.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_woocommerce_currencies( $request ) {

		$currencies = get_woocommerce_currencies();
		$response = array();
		foreach ( $currencies as $code => $name ) {
			$response[] = array(
				'value' => $code,
				'label' => $name . ' (' . $code . ')',
			);
		}
		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Returns the list of checkout currencies.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_checkout_currencies( $request ) {

		$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$has_changed_payment_methods = false;
		$currency_codes = get_woocommerce_currencies();
		$store_currency_code = get_option( 'woocommerce_currency' );
		$currencies = get_option( 'psmwoo_mc_currencies' );
		$checkout_currencies = get_option( 'psmwoo_mc_checkout_currencies' );
		$response = array();
		foreach ( $checkout_currencies as $code => $currency ) {
			// skip disabled currency.
			if ( ! $currencies[ $code ]['enabled'] ) {
				continue;
			}

			// check if payment methods have changed.
			$payment_methods = array();
			foreach ( $currency['paymentMethods'] as $method ) {
				if ( array_key_exists( $method, $payment_gateways ) ) {
					$payment_methods[] = $method;
				} else {
					$has_changed_payment_methods = true;
				}
			}

			$checkout_currencies[ $code ]['paymentMethods'] = $payment_methods;
			$currency['paymentMethods'] = $payment_methods;
			$currency['code'] = $code;
			$currency['name'] = $currency_codes[ $code ];
			$currency['isDefault'] = $code === $store_currency_code;
			$response[] = $currency;
		}

		// update checkout currencies if payment methods have changed.
		if ( $has_changed_payment_methods ) {
			update_option( 'psmwoo_mc_checkout_currencies', $checkout_currencies );
		}

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Updates the checkout currencies.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function update_checkout_currencies( $request ) {
		$checkout_currencies = get_option( 'psmwoo_mc_checkout_currencies' );
		$store_currency_code = get_option( 'woocommerce_currency' );
		$currencies = $request->get_param( 'currencies' );
		foreach ( $checkout_currencies as $code => $currency ) {
			if ( array_key_exists( $code, $currencies ) ) {
				$checkout_currencies[ $code ]['enabled'] = $currencies[ $code ]['enabled'];
				$checkout_currencies[ $code ]['paymentMethods'] = $currencies[ $code ]['paymentMethods'];
			}
		}
		update_option( 'psmwoo_mc_checkout_currencies', $checkout_currencies );

		// return the updated checkout currencies.
		$request = new WP_REST_Request( 'GET', '/psmmc/v1/settings/checkout-currencies' );
		return rest_do_request( $request );
	}

	/**
	 * Returns the list of payment methods.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_payment_methods( $request ) {

		// get list of woocommerce payment methods.
		$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$payment_methods = array();
		foreach ( $payment_gateways as $id => $gateway ) {
			$payment_methods[] = array(
				'value' => $id,
				'label' => $gateway->get_title(),
			);
		}
		return new WP_REST_Response( $payment_methods, 200 );
	}

	/**
	 * Returns the list of checkout options.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_checkout_options( $request ) {

		$checkout_options = get_option( 'psmwoo_mc_checkout_options' );
		return new WP_REST_Response( $checkout_options, 200 );
	}

	/**
	 * Updates the checkout options.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function update_checkout_options( $request ) {

		$checkout_options = array(
			'checkoutDifferentCurrency'     => $request->get_param( 'checkoutDifferentCurrency' ),
			'enableAddressCountryNotice'    => $request->get_param( 'enableAddressCountryNotice' ),
			'addressCountryNotice'          => $request->get_param( 'addressCountryNotice' ),
			'forceByAddressCountry'         => false,
			'addressCountryMethod'          => 'billing',
			'reloadPageAfterCurrencyChange' => false,
		);

		// remove string translations.
		PSMWOO_MC_Translations::remove( 'psmmc-address-country-notice' );
		// add string translations.
		PSMWOO_MC_Translations::add( 'psmmc-address-country-notice', $checkout_options['addressCountryNotice'] );

		update_option( 'psmwoo_mc_checkout_options', $checkout_options );
		return new WP_REST_Response( $checkout_options, 200 );
	}

	/**
	 * Resets the checkout options.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function reset_checkout_options( $request ) {

		$checkout_currencies = get_option( 'psmwoo_mc_checkout_currencies' );
		foreach ( $checkout_currencies as $code => $currency ) {
			$checkout_currencies[ $code ]['enabled'] = true;
			$checkout_currencies[ $code ]['paymentMethods'] = array();
		}
		update_option( 'psmwoo_mc_checkout_currencies', $checkout_currencies );
		PSMWOO_MC_Installation::reset_checkout_options();
		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * Returns the list of display options.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_display_options( $request ) {

		$display_options = get_option( 'psmwoo_mc_display_options' );
		return new WP_REST_Response( $display_options, 200 );
	}

	/**
	 * Updates the display options.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function update_display_options( $request ) {

		$switcher_flag = $request->get_param( 'switcherFlag' );
		$switcher_currency_name = $request->get_param( 'switcherCurrencyName' );
		$switcher_currency_symbol = $request->get_param( 'switcherCurrencySymbol' );
		$switcher_currency_code = $request->get_param( 'switcherCurrencyCode' );

		// at least one of the switcher options must be enabled.
		if ( ! ( $switcher_flag || $switcher_currency_name || $switcher_currency_symbol || $switcher_currency_code ) ) {
			return new WP_Error( 'rest_invalid_param', __( 'At least one switcher attribute must be enabled.', 'psmwoo-multi-currency-pro' ), array( 'status' => 400 ) );
		}

		$display_options = array(
			'enableSingleProduct'    => $request->get_param( 'enableSingleProduct' ),
			'singleProductPosition'  => $request->get_param( 'singleProductPosition' ),
			'switcherSize'           => $request->get_param( 'switcherSize' ),
			'switcherFlag'           => $switcher_flag,
			'switcherCurrencyName'   => $switcher_currency_name,
			'switcherCurrencySymbol' => $switcher_currency_symbol,
			'switcherCurrencyCode'   => $switcher_currency_code,
		);
		update_option( 'psmwoo_mc_display_options', $display_options );
		return new WP_REST_Response( $display_options, 200 );
	}

	/**
	 * Resets the display options.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function reset_display_options( $request ) {

		PSMWOO_MC_Installation::reset_display_options();
		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * Returns the list of auto select country currencies.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_auto_select_country_currencies( $request ) {

		$currency_codes = get_woocommerce_currencies();
		$store_currency_code = get_option( 'woocommerce_currency' );
		$currencies = get_option( 'psmwoo_mc_auto_select_country_currencies' );
		$response = array();
		foreach ( $currencies as $code => $currency ) {
			$currency['code'] = $code;
			$currency['name'] = $currency_codes[ $code ];
			$currency['isDefault'] = $code === $store_currency_code;
			$response[] = $currency;
		}
		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Updates the auto select country currencies.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function update_auto_select_country_currencies( $request ) {

		$auto_select_country_currencies = get_option( 'psmwoo_mc_auto_select_country_currencies' );
		$currencies = $request->get_param( 'currencies' );
		foreach ( $currencies as $code => $currency ) {
			$auto_select_country_currencies[ $code ]['countries'] = $currency['countries'];
		}
		update_option( 'psmwoo_mc_auto_select_country_currencies', $auto_select_country_currencies );

		// return the updated auto select country currencies.
		$request = new WP_REST_Request( 'GET', '/psmmc/v1/settings/auto-select-country-currencies' );
		return rest_do_request( $request );
	}

	/**
	 * Returns the list of advanced settings.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_advanced_settings( $request ) {

		$advanced_settings = get_option( 'psmwoo_mc_advanced_settings' );
		return new WP_REST_Response( $advanced_settings, 200 );
	}

	/**
	 * Updates the advanced settings.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function update_advanced_settings( $request ) {

		$advanced_settings = array(
			'currencyExchangeRateApi'         => $request->get_param( 'currencyExchangeRateApi' ),
			'currencyExchangeRateApiKey'      => '',
			'enableAutoUpdateExchangeRates'   => false,
			'autoUpdateExchangeRatesInterval' => 1,
			'autoUpdateExchangeRatesUnit'     => 'hour',
			'enableAutoSelectCountryCurrency' => false,
			'geoapi'                          => 'ipinfo',
			'enableCachePlugin'               => $request->get_param( 'enableCachePlugin' ),
		);

		// Update exchange rates if the API has changed.
		$settings = get_option( 'psmwoo_mc_advanced_settings' );
		if ( $settings['currencyExchangeRateApi'] !== $advanced_settings['currencyExchangeRateApi'] ) {
			PSMWOO_MC_Helper::update_exchange_rates();
		}

		update_option( 'psmwoo_mc_advanced_settings', $advanced_settings );
		return new WP_REST_Response( $advanced_settings, 200 );
	}

	/**
	 * Resets the advanced settings.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function reset_advanced_settings( $request ) {

		$currencies = get_option( 'psmwoo_mc_auto_select_country_currencies' );
		foreach ( $currencies as $code => $currency ) {
			$currencies[ $code ]['countries'] = array();
		}
		update_option( 'psmwoo_mc_auto_select_country_currencies', $currencies );
		PSMWOO_MC_Installation::reset_advanced_settings();
		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * Returns the list of WooCommerce countries.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_woocommerce_countries( $request ) {

		$wc_countries = WC()->countries->get_countries();
		$response = array();
		foreach ( $wc_countries as $code => $name ) {
			$response[] = array(
				'value' => $code,
				'label' => $name,
			);
		}
		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Returns the REST API response.
	 *
	 * @param array $currencies The currencies.
	 * @return WP_REST_Response
	 */
	public static function get_rest_response( $currencies ) {

		$currency_codes = get_woocommerce_currencies();
		$store_currency_code = get_option( 'woocommerce_currency' );

		$response = array();
		foreach ( $currencies as $code => $currency ) {
			$currency['code'] = $code;
			$currency['name'] = $currency_codes[ $code ];
			$currency['flag'] = PSMWOO_MC_Helper::get_flag_icon_url( $code );
			$currency['isDefault'] = $code === $store_currency_code;
			$response[] = $currency;
		}
		return new WP_REST_Response( $response, 200 );
	}
}

PSMWOO_MC_Rest_Settings::init();
