<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Hooks {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {

		// modify cart contents.
		add_filter( 'woocommerce_get_cart_contents', array( __CLASS__, 'modify_cart_contents' ) );

		// add cart meta data to order.
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'add_custom_cart_item_meta' ), 10, 4 );

		// New order action.
		add_action( 'woocommerce_new_order', array( __CLASS__, 'add_order_currency_meta' ), 10, 2 );

		// psmfr global object data.
		add_action( 'psmfr_js_object', array( __CLASS__, 'add_psmfr_js_object' ) );
	}

	/**
	 * Modify the cart contents.
	 *
	 * @param array $cart_contents The cart contents.
	 * @return array
	 */
	public static function modify_cart_contents( $cart_contents ) {

		$code = PSMWOO_MC_Switcher::get_current_currency();
		$currency = PSMWOO_MC_Switcher::$currencies[ $code ];

		// modify the cart contents.
		foreach ( $cart_contents as $cart_item_key => $cart_item ) {

			$cart_contents[ $cart_item_key ]['_psmwmc_currency'] = $code;
			$cart_contents[ $cart_item_key ]['_psmwmc_exchange_rate'] = $currency['rate'];
			$cart_contents = apply_filters( 'psmwoo_mc_modify_cart_contents', $cart_contents, $cart_item_key, $cart_item );
		}

		return $cart_contents;
	}

	/**
	 * Add custom cart item data to order.
	 *
	 * @param object $item The order item.
	 * @param string $cart_item_key The cart item key.
	 * @param array  $values The cart item values.
	 * @param object $order The order object.
	 * @return void
	 */
	public static function add_custom_cart_item_meta( $item, $cart_item_key, $values, $order ) {

		if ( isset( $values['_psmwmc_currency'] ) && isset( $values['_psmwmc_exchange_rate'] ) ) {
			$order->add_meta_data( '_psmwmc_currency', $values['_psmwmc_currency'], true );
			$order->add_meta_data( '_psmwmc_exchange_rate', $values['_psmwmc_exchange_rate'], true );
		}
	}

	/**
	 * Add order currency meta.
	 *
	 * @param int    $order_id The order ID.
	 * @param object $order The order object.
	 * @return void
	 */
	public static function add_order_currency_meta( $order_id, $order ) {

		$currency = $order->get_currency();
		$default_currency = get_option( 'woocommerce_currency' );
		$exchange_rate = PSMWOO_MC_Helper::get_exchange_rate( $currency );

		$order->add_meta_data( '_psmwoo_mc_currency_rate', $exchange_rate, true );
		$order->add_meta_data( '_psmwoo_mc_base_currency', $default_currency, true );
		$order->save();
	}

	/**
	 * Add currency settings to psmfr global variable.
	 *
	 * @param array $psmfr Existing psmfr object.
	 * @return array
	 */
	public static function add_psmfr_js_object( $psmfr ) {
		$advanced_settings = get_option( 'psmwoo_mc_advanced_settings' );
		$psmfr['psmwmc'] = array(
			'enableCachePlugin' => isset( $advanced_settings['enableCachePlugin'] ) ? $advanced_settings['enableCachePlugin'] : false,
			'currencies'        => PSMWOO_MC_Switcher::$currencies,
			'displayCurrencies' => self::get_display_currencies(),
		);
		return $psmfr;
	}

	/**
	 * Get display currencies.
	 *
	 * @return array
	 */
	public static function get_display_currencies() {

		$currency_codes = get_woocommerce_currencies();
		$response = array();
		foreach ( PSMWOO_MC_Switcher::$currencies as $code => $currency ) {
			// skip disabled currency.
			if ( ! $currency['enabled'] ) {
				continue;
			}
			$response[] = array(
				'code'   => $code,
				'name'   => $currency_codes[ $code ],
				'flag'   => PSMWOO_MC_Helper::get_flag_icon_url( $code ),
				'symbol' => $currency['symbol'],
			);
		}
		return $response;
	}
}

PSMWOO_MC_Hooks::init();
