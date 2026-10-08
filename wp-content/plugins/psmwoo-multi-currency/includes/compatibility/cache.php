<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Compatibility with caching plugins.
 *
 * @since 1.0.0
 */
class PSMWOO_Cache_Compatibility {

	/**
	 * Initialize the class.
	 */
	public static function init() {

		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'wrap_price_html' ), PHP_INT_MAX, 2 );

		add_action( 'wp_ajax_psmwoo_get_prices_for_cache', array( __CLASS__, 'get_prices_for_cache_callback' ) );
		add_action( 'wp_ajax_nopriv_psmwoo_get_prices_for_cache', array( __CLASS__, 'get_prices_for_cache_callback' ) );
	}

	/**
	 * Wrap WooCommerce price HTML for cache compatibility.
	 *
	 * @param string     $price The price HTML.
	 * @param WC_Product $product The product object.
	 * @return string
	 */
	public static function wrap_price_html( $price, $product ) {

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return $price;
		}
		$product_id = $product->get_id();
		$price = self::get_multicurrency_price_html( $product );

		return "<span class='psmwoo-cache-price' data-psmwoo-product-id='{$product_id}'>" . $price . '</span>';
	}

	/**
	 * AJAX callback to get prices for caching.
	 *
	 * @return void
	 */
	public static function get_prices_for_cache_callback() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wp_rest' ) ) {
			wp_send_json_error( array( 'message' => 'Invalid request.' ) );
		}

		if ( empty( $_POST['product_ids'] ) || ! is_array( $_POST['product_ids'] ) ) {
			wp_send_json_error( array( 'message' => 'No product IDs.' ) );
		}

		$product_ids = array_map( 'absint', $_POST['product_ids'] );
		$currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : '';

		$prices = array();
		foreach ( $product_ids as $pid ) {
			$product = wc_get_product( $pid );
			if ( $product ) {
				$prices[ $pid ] = $product->get_price_html();
			}
		}

		wp_send_json_success( $prices );
	}

	/**
	 * Get the price HTML for a product in the current currency.
	 *
	 * @param WC_Product $product The product object.
	 * @return string Price HTML.
	 */
	private static function get_multicurrency_price_html( $product ) {

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return '';
		}

		$product_type = $product->get_type();
		$price_html   = '';

		$currency        = PSMWOO_MC_Switcher::$currency;
		$currency_rate   = (float) PSMWOO_MC_Helper::get_exchange_rate( $currency );
		$currency_symbol = get_woocommerce_currency_symbol( $currency );

		// Format args for wc_price.
		$format_args = array(
			'currency'        => $currency,
			'currency_symbol' => $currency_symbol,
		);

		switch ( $product_type ) {

			case 'simple':
			case 'external':
				$regular = (float) $product->get_regular_price();
				$sale    = (float) $product->get_sale_price();
				$price   = (float) $product->get_price();

				if ( $product->is_on_sale() && $regular > $sale ) {
					$converted_regular = wc_price( $regular * $currency_rate, $format_args );
					$converted_sale    = wc_price( $sale * $currency_rate, $format_args );

					$price_html = wc_format_sale_price( $converted_regular, $converted_sale );
				} else {
					$price_html = wc_price( $price * $currency_rate, $format_args );
				}
				break;

			case 'variable':
				$min_price = (float) $product->get_variation_price( 'min' ) * $currency_rate;
				$max_price = (float) $product->get_variation_price( 'max' ) * $currency_rate;

				if ( $min_price !== $max_price ) {
					$price_html = wc_format_price_range(
						wc_price( $min_price, $format_args ),
						wc_price( $max_price, $format_args )
					);
				} else {
					$price_html = wc_price( $min_price, $format_args );
				}
				break;

			case 'grouped':
				$children     = $product->get_children();
				$child_prices = array();

				foreach ( $children as $child_id ) {
					$child_product = wc_get_product( $child_id );
					if ( $child_product ) {
						$child_price = (float) $child_product->get_price();
						if ( '' !== $child_price ) {
							$child_prices[] = $child_price * $currency_rate;
						}
					}
				}

				if ( ! empty( $child_prices ) ) {
					$min = min( $child_prices );
					$max = max( $child_prices );

					if ( $min !== $max ) {
						$price_html = wc_format_price_range(
							wc_price( $min, $format_args ),
							wc_price( $max, $format_args )
						);
					} else {
						$price_html = wc_price( $min, $format_args );
					}
				}
				break;

			case 'subscription':
			case 'subscription_variation':
				$regular = (float) $product->get_regular_price();
				$sale    = (float) $product->get_sale_price();
				$price   = (float) $product->get_price();

				if ( $product->is_on_sale() && $regular > $sale ) {
					$converted_regular = wc_price( $regular * $currency_rate, $format_args );
					$converted_sale    = wc_price( $sale * $currency_rate, $format_args );

					$price_html = wc_format_sale_price( $converted_regular, $converted_sale );
				} else {
					$price_html = wc_price( $price * $currency_rate, $format_args );
				}
				break;

			default:
				// Fallback for custom/unknown product types: try to get the raw price and format it.
				$raw_price = (float) $product->get_price();
				if ( $raw_price ) {
					$price_html = wc_price( $raw_price * $currency_rate, $format_args );
				} else {
					$price_html = '';
				}
				break;
		}

		return apply_filters( 'psmwoo_get_multicurrency_price_html', $price_html, $product );
	}
}

PSMWOO_Cache_Compatibility::init();
