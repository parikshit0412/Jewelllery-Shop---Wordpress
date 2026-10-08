<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Table_Rate_Shipping {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'woocommerce_table_rate_package_row_base_price', array( __CLASS__, 'table_rate_package_row_base_price' ), 10, 3 );
	}

	/**
	 * Set the base price for the table rate shipping.
	 *
	 * @param float $row_base_price The base price.
	 * @param array $_product The product.
	 * @param array $qty The quantity.
	 * @return float
	 */
	public static function table_rate_package_row_base_price( $row_base_price, $_product, $qty ) {
		return $_product->get_data()['price'] * $qty;
	}
}
PSMWOO_MC_Table_Rate_Shipping::init();
