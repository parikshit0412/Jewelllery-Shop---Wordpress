<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

/**
 * Multi Currency Settings admin page.
 *
 * @package psmwoo-multi-currency
 * @since 1.0.0
 */
class PSMWOO_MC_Settings {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {

		// Add admin settings page.
		add_filter( 'psmplugins_admin_submenus', array( __CLASS__, 'add_settings_page' ) );

		// Enqueue the scripts and styles for the admin page.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		// Update WooCommerce currency.
		add_action( 'update_option_woocommerce_currency', array( __CLASS__, 'update_woocommerce_currency' ), 10, 2 );
	}

	/**
	 * Add settings page.
	 *
	 * @param array $submenus Submenus array.
	 * @return array
	 */
	public static function add_settings_page( $submenus ) {

		$submenus[] = array(
			'page_title' => esc_attr__( 'Multi Currency', 'psmwoo-multi-currency' ),
			'menu_title' => esc_attr__( 'Multi Currency', 'psmwoo-multi-currency' ),
			'capability' => 'manage_woocommerce',
			'menu_slug'  => 'psmwoo-multi-currency-settings',
			'callback'   => array( __CLASS__, 'settings_page_callback' ),
		);

		return $submenus;
	}

	/**
	 * Enqueues the scripts and styles for the admin page.
	 *
	 * This method is called during the admin_enqueue_scripts action and is responsible
	 * for enqueuing the necessary scripts and styles for the admin page.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public static function enqueue_scripts( $hook ) {

		// Load scripts and styles only on the settings page.
		if ( preg_match( '/psmwoo-multi-currency-settings$/', $hook ) ) {
			// Add a class to the body for styling purposes.
			add_filter(
				'admin_body_class',
				function ( $classes ) {
					$classes .= ' toplevel-psmfr-page';
					return $classes;
				}
			);

			// Load asset file.
			$assets = require PSMWOO_MC_ABSPATH . 'build/admin-page/index.asset.php';

			// Enqueue the main admin page styles.
			wp_enqueue_style(
				'psmmc-admin-page',
				PSMWOO_MC_PLUGIN_URL . 'build/admin-page/index' . ( is_rtl() ? '-rtl.css' : '.css' ),
				array(),
				$assets['version']
			);

			// Enqueue the main admin page script.
			wp_enqueue_script(
				'psmmc-admin-page',
				PSMWOO_MC_PLUGIN_URL . 'build/admin-page/index.js',
				$assets['dependencies'],
				$assets['version'],
				true
			);

			// Load script translations.
			wp_set_script_translations( 'psmmc-admin-page', 'psmwoo-multi-currency' );
		}

		// Load scripts and styles for woocommerce analytics custom filters.
		if ( preg_match( '/woocommerce_page_wc-admin$/', $hook ) ) {

			$assets = require PSMWOO_MC_ABSPATH . 'build/admin-page/custom-filters/index.asset.php';
			wp_enqueue_script(
				'psmmc-custom-analytics-filter',
				PSMWOO_MC_PLUGIN_URL . 'build/admin-page/custom-filters/index.js',
				$assets['dependencies'],
				$assets['version'],
				true
			);
		}
	}

	/**
	 * Settings page callback.
	 *
	 * @return void
	 */
	public static function settings_page_callback() {
		echo '<div id="psmmc-admin-page"></div>';
	}

	/**
	 * Update WooCommerce currency.
	 *
	 * @param string $old_value Old currency code.
	 * @param string $new_value New currency code.
	 * @return void
	 */
	public static function update_woocommerce_currency( $old_value, $new_value ) {

		$currencies = get_option( 'psmwoo_mc_currencies' );
		if ( ! empty( $currencies ) && ! array_key_exists( $new_value, $currencies ) ) {
			PSMWOO_MC_Helper::add_new_currency( $new_value );
		}
		self::update_exchange_rate_after_currency_change( $old_value, $new_value );
	}

	/**
	 * Update exchange rate after currency change.
	 *
	 * @param string $old_currency Old currency code.
	 * @param string $new_currency New currency code.
	 * @return void
	 */
	private static function update_exchange_rate_after_currency_change( $old_currency, $new_currency ) {

		$currencies = get_option( 'psmwoo_mc_currencies' );
		$store_currency_code = get_option( 'woocommerce_currency' );
		foreach ( $currencies as $code => $currency ) {

			// exclude if rate mode is manual.
			if ( $currency['rateMode'] === 'manual' ) {
				continue;
			}

			$currencies[ $code ]['rate'] = 1;

			$rate = PSMWOO_MC_Helper::get_exchange_rate_from_api( $store_currency_code, $code );
			if ( $rate ) {
				$currencies[ $code ]['rate'] = $rate;
			}
		}
		update_option( 'psmwoo_mc_currencies', $currencies );
	}
}

PSMWOO_MC_Settings::init();
