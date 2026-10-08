<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Switcher {

	/**
	 * The current currency.
	 *
	 * @var string
	 */
	public static $currency = '';

	/**
	 * All possible currencies for the switcher.
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
		add_action( 'init', array( __CLASS__, 'set_current_currency' ), 2 );
		add_action( 'init', array( __CLASS__, 'register_woo_currency_hooks' ) );
	}

	/**
	 * Register the WooCommerce currency hooks.
	 *
	 * @return void
	 */
	public static function register_woo_currency_hooks() {

		// frontend only hooks.
		if ( ! is_admin() ) {

			// Set the currency, currency position, thousand separator, decimal separator and decimals.
			add_filter( 'woocommerce_currency', array( __CLASS__, 'set_currency' ) );
			add_filter( 'pre_option_woocommerce_currency_pos', array( __CLASS__, 'set_currency_position' ) );
			add_filter( 'wc_get_price_thousand_separator', array( __CLASS__, 'set_thousand_separator' ) );
			add_action( 'wc_get_price_decimal_separator', array( __CLASS__, 'set_decimal_separator' ) );
			add_filter( 'wc_get_price_decimals', array( __CLASS__, 'set_decimals' ) );

			// Set product price.
			add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_variation_prices_price', array( __CLASS__, 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_variation_prices_regular_price', array( __CLASS__, 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_variation_prices_sale_price', array( __CLASS__, 'set_product_price' ), 10, 2 );

			// shipping.
			add_filter( 'woocommerce_package_rates', array( __CLASS__, 'set_shipping_price' ), 10, 2 );
			add_filter( 'woocommerce_shipping_free_shipping_instance_option', array( __CLASS__, 'woo_fs_min_amount_convert' ), 20, 3 );
			add_filter( 'woocommerce_shipping_free_shipping_option', array( __CLASS__, 'woo_fs_min_amount_convert' ), 20, 3 );

			// Coupons.
			add_action( 'woocommerce_coupon_get_discount_amount', array( __CLASS__, 'coupon_get_discount_amount' ), 10, 5 );
		}
	}

	/**
	 * Set the currencies.
	 *
	 * @return void
	 */
	public static function set_currencies() {

		$currencies = get_option( 'psmwoo_mc_currencies' );
		self::$currencies = $currencies;
	}

	/**
	 * Set the current currency.
	 *
	 * @return void
	 */
	public static function set_current_currency() {

		$currency = '';

		// get currency from the cookie 'psmmc_currency' if set.
		if ( isset( $_COOKIE['psmmc_currency'] ) ) {
			$currency = sanitize_text_field( wp_unslash( $_COOKIE['psmmc_currency'] ) );
		} else {
			// change product transient value, so that prices will be updated in the cache.
			delete_transient( 'product-transient-version' );
		}

		// set store currency if not set.
		if ( empty( $currency ) || ! array_key_exists( $currency, self::$currencies ) ) {
			$currency = get_option( 'woocommerce_currency' );
		}

		self::$currency = $currency;
		PSMWOO_MC_Helper::set_cookie_currency( $currency );
	}

	/**
	 * Get the current currency.
	 *
	 * @return string
	 */
	public static function get_current_currency() {

		return PSMWOO_MC_Helper::is_checkout() ? PSMWOO_MC_Checkout_Currency::$currency : self::$currency;
	}

	/**
	 * Render the switcher.
	 *
	 * @param array $attributes The attributes of the switcher.
	 * @return void
	 */
	public static function render( $attributes ) {
		wp_enqueue_script( 'psmmc-switcher' );
		wp_enqueue_style( 'psmmc-switcher' );
		?>
		<div class="psmmc-switcher" data-preset='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'></div>
		<?php
	}

	/**
	 * Set the woocommerce currency.
	 *
	 * @param string $currency The currency.
	 * @return string
	 */
	public static function set_currency( $currency ) {
		return self::get_current_currency();
	}

	/**
	 * Set the currency position.
	 *
	 * @param string $position The currency position.
	 * @return string
	 */
	public static function set_currency_position( $position ) {

		$currency = self::$currencies[ self::get_current_currency() ];
		return $currency['currencyPosition'];
	}

	/**
	 * Set the price thousand separator.
	 *
	 * @param string $separator The thousand separator.
	 * @return string
	 */
	public static function set_thousand_separator( $separator ) {

		$currency = self::$currencies[ self::get_current_currency() ];
		return $currency['thousandSeparator'];
	}

	/**
	 * Set the price decimal separator.
	 *
	 * @param string $separator The decimal separator.
	 * @return string
	 */
	public static function set_decimal_separator( $separator ) {

		$currency = self::$currencies[ self::get_current_currency() ];
		return $currency['decimalSeparator'];
	}

	/**
	 * Set the price decimals.
	 *
	 * @param int $decimals The decimals.
	 * @return int
	 */
	public static function set_decimals( $decimals ) {

		$currency = self::$currencies[ self::get_current_currency() ];
		return $currency['decimal'];
	}

	/**
	 * Set the product price.
	 *
	 * @param float  $price The product price.
	 * @param object $product The product object.
	 * @return float
	 */
	public static function set_product_price( $price, $product ) {

		$current_currency = self::get_current_currency();
		$advanced = get_option( 'psmwoo_mc_advanced_settings' );

		// get the product price in the default currency.
		$price = apply_filters( 'psmwoo_mc_get_default_product_price', $price, $product );

		// return the price if the current currency is the default currency.
		if ( PSMWOO_MC_Helper::is_default_currency( $current_currency ) || $advanced['enableCachePlugin'] ) {
			return $price;
		}

		// Check whether it needs to use the original price in some cases. For example, json request for the products.
		if ( PSMWOO_MC_Helper::need_original_product_price( false, $price, $product ) ) {
			return $price;
		}

		// convert the price.
		$price = self::convert( $price );
		return apply_filters( 'psmwoo_mc_get_converted_product_price', $price, $product, self::get_current_currency() );
	}

	/**
	 * Set the shipping price.
	 *
	 * @param array $rates The shipping rates.
	 * @param array $package The package.
	 * @return array
	 */
	public static function set_shipping_price( $rates, $package ) {

		$current_currency = self::get_current_currency();

		// return the price if the current currency is the default currency.
		if ( PSMWOO_MC_Helper::is_default_currency( $current_currency ) ) {
			return $rates;
		}

		foreach ( $rates as $rate_id => $rate ) {
			$cost = self::convert( $rate->cost );
			$rates[ $rate_id ]->cost = $cost;
		}

		return $rates;
	}

	/**
	 * Custom free shipping min amount.
	 *
	 * @param string $option The option.
	 * @param string $key The method.
	 * @param array  $method The package.
	 * @return string
	 */
	public static function woo_fs_min_amount_convert( $option, $key, $method ) {

		$current_currency = self::get_current_currency();

		// return the option if the current currency is the default currency.
		if ( PSMWOO_MC_Helper::is_default_currency( $current_currency ) ) {
			return $option;
		}

		if ( 'min_amount' !== $key || empty( $option ) || ! is_numeric( $option ) ) {
			return $option;
		}

		return self::convert( $option );
	}

	/**
	 * Get the discount amount.
	 *
	 * @param float  $discount The discount amount.
	 * @param float  $discounting_amount The discounting amount.
	 * @param array  $cart_item The cart item.
	 * @param bool   $single If the discount is for a single item.
	 * @param object $coupon The coupon object.
	 * @return float
	 */
	public static function coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

		$current_currency = self::get_current_currency();

		// return the discount if the current currency is the default currency.
		if ( PSMWOO_MC_Helper::is_default_currency( $current_currency ) ) {
			return $discount;
		}

		if ( empty( $discount ) || ! is_numeric( $discount ) ) {
			return $discount;
		}

		$discount_type = $coupon->get_discount_type();
		if ( 'percent' === $discount_type ) {
			$subtotal = WC()->cart->get_subtotal();
			return ( $subtotal * $coupon->get_amount() ) / 100;
		}

		// get the discount amount in the default currency if the discount type is other than core wooocommerce types.
		if ( ! in_array( $discount_type, array( 'fixed_cart', 'fixed_product' ) ) ) {
			$discount = apply_filters( 'psmwoo_mc_get_default_coupon_discount_amount', $discount, $discounting_amount, $cart_item, $single, $coupon );
		}

		return self::convert( $discount );
	}

	/**
	 * Convert the price.
	 *
	 * @param float $price The price.
	 * @return float
	 */
	public static function convert( $price ) {

		if ( empty( $price ) || ! is_numeric( $price ) ) {
			return $price;
		}

		$currency = self::$currencies[ self::get_current_currency() ];

		// apply currency exchange rate.
		$price = floatval( $price ) * floatval( $currency['rate'] );
		if ( ! $price ) {
			return $price;
		}

		// add fee.
		if ( $currency['fee'] ) {
			if ( 'percentage' === $currency['feeMode'] ) {
				$price += ( $price * floatval( $currency['fee'] ) ) / 100;
			} else {
				$price += floatval( $currency['fee'] );
			}
		}

		// round the price.
		if ( 'disabled' !== $currency['rounding'] ) {

			// round the price.
			$rounding_to = floatval( $currency['roundingTo'] );
			switch ( $currency['rounding'] ) {

				case 'up':
					$price = ceil( $price / $rounding_to ) * $rounding_to;
					break;

				case 'down':
					$price = floor( $price / $rounding_to ) * $rounding_to;
					break;

				case 'nearest':
					$price = round( $price / $rounding_to ) * $rounding_to;
					break;
			}

			// deduct the rounding minus.
			$rounding_minus = floatval( $currency['roundingMinus'] );
			if ( $rounding_minus ) {
				$price -= $rounding_minus;
			}
		}

		return $price;
	}
}

PSMWOO_MC_Switcher::init();
