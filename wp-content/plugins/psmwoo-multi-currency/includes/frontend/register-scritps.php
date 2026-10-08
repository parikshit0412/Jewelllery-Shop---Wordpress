<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Scripts {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
	}

	/**
	 * Register the scripts.
	 *
	 * @return void
	 */
	public static function register_scripts() {

		$checkout_options = get_option( 'psmwoo_mc_checkout_options' );
		$advanced_settings = get_option( 'psmwoo_mc_advanced_settings' );

		// switcher styles and scripts.
		$assets = require PSMWOO_MC_ABSPATH . 'build/switcher/index.asset.php';
		wp_register_style(
			'psmmc-switcher',
			PSMWOO_MC_PLUGIN_URL . 'build/switcher/index' . ( is_rtl() ? '-rtl.css' : '.css' ),
			array(),
			$assets['version']
		);
		wp_register_script(
			'psmmc-switcher',
			PSMWOO_MC_PLUGIN_URL . 'build/switcher/index.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);
		wp_add_inline_script(
			'psmmc-switcher',
			'var psmmcCurrentCurrency = null;'
		);
		wp_set_script_translations( 'psmmc-switcher', 'psmwoo-multi-currency' );

		// current currency script.
		$assets = require PSMWOO_MC_ABSPATH . 'build/js/current-currency.asset.php';
		wp_enqueue_script(
			'psmmc-current-currency',
			PSMWOO_MC_PLUGIN_URL . 'build/js/current-currency.js',
			array( 'psmmc-switcher' ),
			$assets['version'],
			false
		);

		// checkout notice script.
		if ( PSMWOO_MC_Helper::is_checkout() && $checkout_options['checkoutDifferentCurrency'] ) {
			$assets = require PSMWOO_MC_ABSPATH . 'build/js/checkout.asset.php';
			wp_enqueue_script(
				'psmmc-checkout-notice',
				PSMWOO_MC_PLUGIN_URL . 'build/js/checkout.js',
				array( 'jquery', 'wc-checkout' ),
				$assets['version'],
				false
			);
			wp_add_inline_script(
				'psmmc-checkout-notice',
				'var psmmcCheckoutData = ' . wp_json_encode( PSMWOO_MC_Checkout_Currency::get_js_data() ) . ';',
				'before'
			);
		}

		// Caching compatibility script.
		if ( isset( $advanced_settings['enableCachePlugin'] ) && $advanced_settings['enableCachePlugin'] ) {
			$assets = require PSMWOO_MC_ABSPATH . 'build/js/cache-price.asset.php';
			wp_enqueue_script(
				'psmwoo-cache-price',
				PSMWOO_MC_PLUGIN_URL . 'build/js/cache-price.js',
				array( 'jquery-core' ),
				$assets['version'],
				false
			);
		}
	}
}

PSMWOO_MC_Scripts::init();
