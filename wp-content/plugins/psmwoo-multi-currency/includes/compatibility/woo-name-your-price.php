<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Compatibility with WooCommerce Name Your Price plugin.
 *
 * @since 1.0.0
 */
class PSMWOO_MC_Woo_Name_Your_Price {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {

		add_filter( 'wc_nyp_raw_suggested_price', array( __CLASS__, 'wc_nyp_raw_suggested_price' ), 10, 3 );
		add_filter( 'wc_nyp_raw_minimum_price', array( __CLASS__, 'wc_nyp_raw_minimum_price' ), 10, 3 );
		add_filter( 'wc_nyp_raw_maximum_price', array( __CLASS__, 'wc_nyp_raw_maximum_price' ), 10, 3 );

		add_filter( 'psmwoo_mc_need_original_product_price', array( __CLASS__, 'need_original_product_price' ), 10, 3 );
	}

	/**
	 * Filter to determine if the original product price is needed.
	 *
	 * @param bool   $flag The flag to determine if the original product price is needed.
	 * @param float  $price The product price.
	 * @param object $product The product object.
	 * @return bool
	 */
	public static function need_original_product_price( $flag, $price, $product ) {

		if ( WC_Name_Your_Price_Helpers::is_nyp( $product ) ) {
			$flag = true;
		}
		return $flag;
	}

	/**
	 * Filter the raw suggested price.
	 *
	 * @param float  $price The raw suggested price.
	 * @param float  $product_id The product ID.
	 * @param string $type The type of price.
	 * @return float
	 */
	public static function wc_nyp_raw_suggested_price( $price, $product_id, $type ) {
		$current_currency = PSMWOO_MC_Switcher::get_current_currency();
		$rate = PSMWOO_MC_Helper::get_exchange_rate( $current_currency );
		$suggested_price = $price * $rate;
		return $suggested_price;
	}

	/**
	 * Filter the raw minimum price.
	 *
	 * @param float  $minimum The raw minimum price.
	 * @param float  $product_id The product ID.
	 * @param object $product The product object.
	 * @return float
	 */
	public static function wc_nyp_raw_minimum_price( $minimum, $product_id, $product ) {
		$current_currency = PSMWOO_MC_Switcher::get_current_currency();
		$rate = PSMWOO_MC_Helper::get_exchange_rate( $current_currency );
		$minimum_price = $minimum * $rate;
		return $minimum_price;
	}

	/**
	 * Filter the raw maximum price.
	 *
	 * @param float  $maximum The raw maximum price.
	 * @param float  $product_id The product ID.
	 * @param object $product The product object.
	 * @return float
	 */
	public static function wc_nyp_raw_maximum_price( $maximum, $product_id, $product ) {
		$current_currency = PSMWOO_MC_Switcher::get_current_currency();
		$rate = PSMWOO_MC_Helper::get_exchange_rate( $current_currency );
		$maximum_price = $maximum * $rate;
		return $maximum_price;
	}
}
PSMWOO_MC_Woo_Name_Your_Price::init();
