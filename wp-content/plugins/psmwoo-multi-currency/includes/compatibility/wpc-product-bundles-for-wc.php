<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Compatibility with WPC Product Bundles for WooCommerce plugin.
 *
 * @since 1.0.0
 */
class PSMWOO_WPC_Product_Bundles {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'psmwoo_mc_need_original_product_price', array( __CLASS__, 'get_original_product_price' ), 10, 3 );
	}

	/**
	 * Determine whether to use the original product price.
	 *
	 * @param bool       $need_original_price Whether to use the original price.
	 * @param float      $price The product price.
	 * @param WC_Product $product The product object.
	 * @return bool
	 */
	public static function get_original_product_price( $need_original_price, $price, $product ) {

		$changes = $product->get_changes();
		if ( isset( $changes['price'] ) ) {
			return true;
		}
		return $need_original_price;
	}
}

PSMWOO_WPC_Product_Bundles::init();
