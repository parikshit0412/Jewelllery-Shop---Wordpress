<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Compatibility with WooCommerce Subscriptions plugin.
 *
 * @since 1.0.0
 */
class PSMWOO_MC_Woo_Product_Addons {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {

		add_filter( 'woocommerce_product_addons_option_price_raw', array( __CLASS__, 'product_addons_option_price_raw' ), 10, 3 );
		add_filter( 'woocommerce_product_addons_price_raw', array( __CLASS__, 'product_addons_price_raw' ), 10, 2 );
	}

	/**
	 * Set the product price for the product addons.
	 *
	 * @param float $price The product price.
	 * @param array $option The product option.
	 * @return float
	 */
	public static function product_addons_option_price_raw( $price, $option ) {

		if ( $option['price_type'] !== 'percentage_based' ) {
			$price = PSMWOO_MC_Switcher::set_product_price( $price, $option );
		}
		return $price;
	}

	/**
	 * Set the product price for the product addons.
	 *
	 * @param float $addon_price The product price.
	 * @param array $addon The product addon.
	 * @return float
	 */
	public static function product_addons_price_raw( $addon_price, $addon ) {

		if ( $addon['price_type'] !== 'percentage_based' ) {
			$addon_price = PSMWOO_MC_Switcher::set_product_price( $addon_price, $addon );
		}
		return $addon_price;
	}
}

PSMWOO_MC_Woo_Product_Addons::init();
