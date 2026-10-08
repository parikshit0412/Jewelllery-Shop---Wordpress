<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Shortcode {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {

		$display_options = get_option( 'psmwoo_mc_display_options' );
		if ( $display_options['enableSingleProduct'] ) {
			if ( 'before-short-description' === $display_options['singleProductPosition'] ) {
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'render_single_product' ) );
			} else {
				add_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'render_single_product' ) );
			}
		}
		add_shortcode( 'psm-multi-currency-switcher', array( __CLASS__, 'render_shortcode' ) );
	}

	/**
	 * Render the single product switcher.
	 *
	 * @return string
	 */
	public static function render_single_product() {
		$attributes = self::get_shortcode_attributes();
		if ( is_wp_error( $attributes ) ) {
			return '';
		}
		PSMWOO_MC_Switcher::render( $attributes );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array $attrs The shortcode attributes.
	 * @return string
	 */
	public static function render_shortcode( $attrs ) {
		ob_start();
		$attributes = self::get_shortcode_attributes( $attrs );
		if ( is_wp_error( $attributes ) ) {
			return '';
		}
		PSMWOO_MC_Switcher::render( $attributes );
		return ob_get_clean();
	}

	/**
	 * Get the shortcode attributes.
	 *
	 * @param array $attrs The shortcode attributes.
	 * @return array
	 */
	public static function get_shortcode_attributes( $attrs = array() ) {

		$attributes = array();
		if ( ! empty( $attrs ) ) {

			$switcher_size            = isset( $attrs['size'] ) ? $attrs['size'] : 'medium';
			$switcher_flag            = isset( $attrs['flag'] ) ? (bool) $attrs['flag'] : false;
			$switcher_currency_name   = isset( $attrs['name'] ) ? (bool) $attrs['name'] : false;
			$switcher_currency_symbol = isset( $attrs['symbol'] ) ? (bool) $attrs['symbol'] : false;
			$switcher_currency_code   = isset( $attrs['code'] ) ? (bool) $attrs['code'] : false;

			// If none are enabled, return error.
			if ( ! ( $switcher_flag || $switcher_currency_name || $switcher_currency_symbol || $switcher_currency_code ) ) {
				return new WP_Error( 'invalid_switcher_attributes', __( 'At least one switcher attribute must be enabled.', 'psmwoo-multi-currency-pro' ), array( 'status' => 400 ) );
			}

			$attributes = array(
				'switcherSize'           => $switcher_size,
				'switcherFlag'           => $switcher_flag,
				'switcherCurrencyName'   => $switcher_currency_name,
				'switcherCurrencySymbol' => $switcher_currency_symbol,
				'switcherCurrencyCode'   => $switcher_currency_code,
			);
		} else {

			$display_options          = get_option( 'psmwoo_mc_display_options' );
			$switcher_size            = isset( $display_options['switcherSize'] ) ? $display_options['switcherSize'] : 'medium';
			$switcher_flag            = isset( $display_options['switcherFlag'] ) ? (bool) $display_options['switcherFlag'] : false;
			$switcher_currency_name   = isset( $display_options['switcherCurrencyName'] ) ? (bool) $display_options['switcherCurrencyName'] : false;
			$switcher_currency_symbol = isset( $display_options['switcherCurrencySymbol'] ) ? (bool) $display_options['switcherCurrencySymbol'] : false;
			$switcher_currency_code   = isset( $display_options['switcherCurrencyCode'] ) ? (bool) $display_options['switcherCurrencyCode'] : false;

			// If none are enabled, return error.
			if ( ! ( $switcher_flag || $switcher_currency_name || $switcher_currency_symbol || $switcher_currency_code ) ) {
				return new WP_Error( 'invalid_switcher_attributes', __( 'At least one switcher attribute must be enabled.', 'psmwoo-multi-currency-pro' ), array( 'status' => 400 ) );
			}

			$attributes = array(
				'switcherSize'           => $switcher_size,
				'switcherFlag'           => $switcher_flag,
				'switcherCurrencyName'   => $switcher_currency_name,
				'switcherCurrencySymbol' => $switcher_currency_symbol,
				'switcherCurrencyCode'   => $switcher_currency_code,
			);
		}

		return $attributes;
	}
}

add_action( 'init', array( 'PSMWOO_MC_Shortcode', 'init' ) );
