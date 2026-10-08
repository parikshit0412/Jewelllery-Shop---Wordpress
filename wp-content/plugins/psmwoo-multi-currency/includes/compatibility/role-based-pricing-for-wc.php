<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}
/**
 * Compatibility with Role Based Pricing for WooCommerce plugin.
 *
 * @since 1.0.0
 */
class PSMWOO_MC_Role_Based_Pricing {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'psmwoo_mc_addify_get_price_html' ), 200, 2 );
	}

	/**
	 * Filter product price HTML for role-based pricing and currency conversion.
	 *
	 * @param string     $price_html The original price HTML.
	 * @param WC_Product $product The product object.
	 * @return string Modified price HTML.
	 */
	public static function psmwoo_mc_addify_get_price_html( $price_html, $product ) {
		$afc_sp_price = new \AF_C_S_P_Price();
		$user = wp_get_current_user();
		$user_roles = (array) $user->roles;
		$user_role = ! empty( $user_roles ) ? $user_roles[0] : 'guest';

		$role_based_price = $afc_sp_price->get_price_of_product( $product, $user, $user_role );
		$price = ! is_bool( $role_based_price ) ? floatval( $role_based_price ) : $role_based_price;

		if ( ! empty( $price ) ) {
			$price = self::psmwoo_mc_get_price_in_current_currency( $price, $product );
			return wc_price( $price ) . $product->get_price_suffix();
		}

		return $price_html;
	}

	/**
	 * Convert a price to the current currency using plugin settings.
	 *
	 * @param float               $price The price in the store's base currency.
	 * @param WC_Product|int|null $product The product object or ID (optional).
	 * @return float Converted price.
	 */
	public static function psmwoo_mc_get_price_in_current_currency( $price, $product = null ) {
		if ( ! is_numeric( $price ) ) {
			return $price;
		}

		// Try to get the current currency and conversion rate.
		$current_currency = null;
		$rate = 1;
		if ( class_exists( 'PSMWOO_MC_Switcher' ) ) {
			if ( method_exists( 'PSMWOO_MC_Switcher', 'get_current_currency' ) ) {
				$current_currency = PSMWOO_MC_Switcher::get_current_currency();
			}
			if ( isset( $current_currency ) && method_exists( 'PSMWOO_MC_Helper', 'get_exchange_rate' ) ) {
				$rate = PSMWOO_MC_Helper::get_exchange_rate( $current_currency );
			}
		}

		// Fallback to convert() if available.
		if ( class_exists( 'PSMWOO_MC_Switcher' ) && method_exists( 'PSMWOO_MC_Switcher', 'convert' ) ) {
			return PSMWOO_MC_Switcher::convert( $price );
		}

		// Manual conversion fallback.
		return floatval( $price ) * floatval( $rate );
	}
}
PSMWOO_MC_Role_Based_Pricing::init();
