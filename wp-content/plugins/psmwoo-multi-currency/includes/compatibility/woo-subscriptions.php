<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Compatibility with WooCommerce Subscriptions plugin.
 *
 * @since 1.0.0
 */
class PSMWOO_MC_Woo_Subscriptions {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {

		// frontend only hooks.
		if ( ! is_admin() ) {
			add_filter( 'woocommerce_subscriptions_product_sign_up_fee', array( 'PSMWOO_MC_Switcher', 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_subscriptions_product_price', array( 'PSMWOO_MC_Switcher', 'set_product_price' ), 10, 2 );
			add_filter( 'woocommerce_subscriptions_product_sale_price', array( __CLASS__, 'product_sale_price' ), 10, 2 );
		}

		add_filter( 'psmwoo_mc_modify_cart_contents', array( __CLASS__, 'modify_cart_contents' ), 10, 3 );
		add_filter( 'psmwoo_mc_get_default_product_price', array( __CLASS__, 'get_default_product_price' ), 10, 2 );

		// coupon discount.
		add_filter( 'psmwoo_mc_get_default_coupon_discount_amount', array( __CLASS__, 'get_default_coupon_discount_amount' ), 10, 5 );
	}

	/**
	 * Set the sale price for the subscription product.
	 * It fixed the issue of incorrect sale price on the subscription product for currency different than the base currency.
	 * While calculating the product price in the woo-subscriptions plugin, it uses the regular price directly from meta data (not converted to current currency)
	 * and sale price from $product->get_sale_price( 'view' ) which is converted to current currency due to the 'view' context.
	 *
	 * @param float  $sale_price The sale price.
	 * @param object $product The product object.
	 * @return float
	 */
	public static function product_sale_price( $sale_price, $product ) {

		return $product->get_sale_price( 'edit' );
	}

	/**
	 * Modify the cart contents.
	 *
	 * @param array  $cart_contents The cart contents.
	 * @param string $cart_item_key The cart item key.
	 * @param array  $cart_item The cart item.
	 * @return array
	 */
	public static function modify_cart_contents( $cart_contents, $cart_item_key, $cart_item ) {

		// detect subscription renewal.
		if ( isset( $cart_item['subscription_renewal'] ) && isset( $cart_item['subscription_renewal']['renewal_order_id'] ) ) {
			$renewal_order_id = $cart_item['subscription_renewal']['renewal_order_id'];
			$subscription = wc_get_order( $renewal_order_id );
			$currency = $subscription->get_currency();
			if ( ! PSMWOO_MC_Helper::is_default_currency( $currency ) ) {
				$price = $cart_item['data']->get_price( 'edit' );
				$exchange_rate = self::get_exchange_rate( $subscription, $currency );
				$price = (float) ( $price / $exchange_rate );
				$cart_contents[ $cart_item_key ]['data']->update_meta_data( '_psmwmc_subscription_renewal_price', $price );
			}
		}

		// detect subscription resubscribe.
		if ( isset( $cart_item['subscription_resubscribe'] ) && isset( $cart_item['subscription_resubscribe']['subscription_id'] ) ) {
			$subscription_id = $cart_item['subscription_resubscribe']['subscription_id'];
			$subscription = wcs_get_subscription( $subscription_id );
			$currency = $subscription->get_currency();
			if ( ! PSMWOO_MC_Helper::is_default_currency( $currency ) ) {
				$price = $cart_item['data']->get_price( 'edit' );
				$exchange_rate = self::get_exchange_rate( $subscription, $currency );
				$price = (float) ( $price / $exchange_rate );
				$cart_contents[ $cart_item_key ]['data']->update_meta_data( '_psmwmc_subscription_resubscribe_price', $price );
			}
		}

		return $cart_contents;
	}

	/**
	 * Get the default product price.
	 *
	 * @param float  $price The product price.
	 * @param object $product The product object.
	 * @return float
	 */
	public static function get_default_product_price( $price, $product ) {

		if ( is_object( $product ) && $product->is_type( 'subscription' ) ) {
			$renewal_price = $product->get_meta( '_psmwmc_subscription_renewal_price' );
			if ( $renewal_price ) {
				return $renewal_price;
			}

			$resubscribe_price = $product->get_meta( '_psmwmc_subscription_resubscribe_price' );
			if ( $resubscribe_price ) {
				return $resubscribe_price;
			}
		}

		return $price;
	}

	/**
	 * Get the exchange rate for the subscription.
	 *
	 * @param object $subscription The subscription object.
	 * @param string $currency_code The currency code.
	 * @return float
	 */
	private static function get_exchange_rate( $subscription, $currency_code ) {

		$currency = PSMWOO_MC_Switcher::$currencies[ $currency_code ] ?? null;
		if ( $currency ) {
			return $currency['rate'];
		}
		$exchange_rate = $subscription->get_meta( '_psmwmc_exchange_rate' );
		return $exchange_rate ? $exchange_rate : 1;
	}

	/**
	 * Get the default coupon discount amount.
	 *
	 * @param float  $discount The discount amount.
	 * @param float  $discounting_amount The discounting amount.
	 * @param array  $cart_item The cart item.
	 * @param bool   $single Is single product.
	 * @param object $coupon The coupon object.
	 * @return float
	 */
	public static function get_default_coupon_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

		$discount_type = $coupon->get_discount_type();
		$amount = $coupon->get_amount();

		if ( 'sign_up_fee' === $discount_type ) {
			$signup_fee = $cart_item['data']->get_meta( '_subscription_sign_up_fee' );
			return $signup_fee < $amount ? $signup_fee : $amount;
		}

		if ( 'sign_up_fee_percent' === $discount_type ) {
			$signup_fee = $cart_item['data']->get_meta( '_subscription_sign_up_fee' );
			return ( $signup_fee * $amount ) / 100;
		}

		if ( 'recurring_percent' === $discount_type ) {
			$price = $cart_item['data']->get_price( 'edit' );
			return ( $price * $amount ) / 100;
		}

		return $discount;
	}
}

PSMWOO_MC_Woo_Subscriptions::init();
