<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Compatibility with WooCommerce Product Bundles plugin.
 *
 * @since 1.0.0
 */
class PSMWOO_MC_Woo_Product_Bundles {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {

		add_filter( 'woocommerce_subscriptions_product_price_string', array( __CLASS__, 'product_price_string' ), 10, 3 );
	}

	/**
	 * Filter the product price string for WooCommerce Subscriptions.
	 *
	 * @param string     $subscription_string The product price string.
	 * @param WC_Product $product The product object.
	 * @param array      $args The arguments.
	 * @return string
	 */
	public static function product_price_string( $subscription_string, $product, $args ) {

		$initial_price = $product->get_price();
		$regular_price = $product->get_regular_price();
		$billing_period = WC_Subscriptions_Product::get_period( $product );

		if ( $regular_price > $initial_price ) {
			$formatted_price = sprintf(
				'<del>%s</del> %s',
				wc_price( $regular_price ),
				wc_price( $initial_price )
			);
		} else {
			$formatted_price = wc_price( $initial_price );
		}

		return sprintf( '%s <span class="subscription-details"> / %s</span>', $formatted_price, $billing_period );
	}
}

PSMWOO_MC_Woo_Product_Bundles::init();
