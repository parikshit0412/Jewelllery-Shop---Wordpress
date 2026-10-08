<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'PSMWOO_MC_Installation' ) ) :

	final class PSMWOO_MC_Installation {

		/**
		 * Currently installed version
		 *
		 * @var integer
		 */
		public static $current_version;

		/**
		 * For checking whether upgrade available or not
		 *
		 * @var boolean
		 */
		public static $is_upgrade = false;

		/**
		 * Initialize installation
		 */
		public static function init() {

			self::get_current_version();
			self::check_upgrade();

			if ( self::$is_upgrade ) {

				// Do not allow parallel process to run.
				if ( 'yes' === get_transient( 'psmwoo_mc_installing' ) ) {
					return;
				}

				// Set transient.
				set_transient( 'psmwoo_mc_installing', 'yes', MINUTE_IN_SECONDS * 10 );

				// Create database tables.
				self::create_db_tables();

				// Run installation.
				if ( self::$current_version == 0 ) {
					add_action( 'init', array( __CLASS__, 'initial_setup' ), 1 );
				} else {
					add_action( 'init', array( __CLASS__, 'upgrade' ), 1 );
				}

				// Delete transient.
				delete_transient( 'psmwoo_mc_installing' );
			}
		}

		/**
		 * Check version
		 */
		public static function get_current_version() {
			self::$current_version = get_option( 'psmwoo_mc_current_version', 0 );
		}

		/**
		 * Check for upgrade
		 */
		public static function check_upgrade() {
			if ( self::$current_version != PSMWOO_MC_VERSION ) {
				self::$is_upgrade = true;
			}
		}

		/**
		 * First time installation
		 */
		public static function initial_setup() {

			$currency_code = get_option( 'woocommerce_currency' );

			// currencies.
			update_option(
				'psmwoo_mc_currencies',
				array(
					$currency_code => array(
						'enabled'           => true,
						'symbol'            => get_woocommerce_currency_symbol( $currency_code ),
						'currencyPosition'  => get_option( 'woocommerce_currency_pos' ),
						'thousandSeparator' => get_option( 'woocommerce_price_thousand_sep' ),
						'decimalSeparator'  => get_option( 'woocommerce_price_decimal_sep' ),
						'decimal'           => absint( get_option( 'woocommerce_price_num_decimals' ) ),
						'rounding'          => 'disabled',
						'roundingTo'        => 1,
						'roundingMinus'     => 0,
						'rate'              => 1,
						'rateMode'          => 'auto',
						'fee'               => 0,
						'feeMode'           => 'fixed',
					),
				)
			);

			// checkout currencies.
			update_option(
				'psmwoo_mc_checkout_currencies',
				array(
					$currency_code => array(
						'enabled'        => true,
						'paymentMethods' => array(),
					),
				)
			);

			// auto select country currencies.
			update_option(
				'psmwoo_mc_auto_select_country_currencies',
				array(
					$currency_code => array(
						'countries' => array(),
					),
				)
			);

			// checkout options.
			self::reset_checkout_options();

			// display options.
			self::reset_display_options();

			// advanced options.
			self::reset_advanced_settings();

			self::set_upgrade_complete();
		}

		/**
		 * Upgrade the version
		 */
		public static function upgrade() {

			if ( version_compare( self::$current_version, '1.0.2', '<' ) ) {
				$currencies = get_option( 'psmwoo_mc_currencies', array() );
				foreach ( $currencies as $code => $currency ) {
					if ( ! isset( $currency['enabled'] ) ) {
						$currencies[ $code ]['enabled'] = true;
					}
				}
				update_option( 'psmwoo_mc_currencies', $currencies );
			}
			self::set_upgrade_complete();
		}

		/**
		 * Mark upgrade as complete
		 */
		public static function set_upgrade_complete() {

			update_option( 'psmwoo_mc_current_version', PSMWOO_MC_VERSION );
			self::$current_version = PSMWOO_MC_VERSION;
			self::$is_upgrade      = false;
		}

		/**
		 * Create database table
		 *
		 * @return void
		 */
		public static function create_db_tables() {
		}

		/**
		 * Reset checkout settings
		 *
		 * @return void
		 */
		public static function reset_checkout_options() {

			$notice = 'Paying in <strong>%currency-selected%</strong> is not supported in your location. So your payment will be recorded in <strong>%checkout-currency%</strong>.';
			update_option(
				'psmwoo_mc_checkout_options',
				array(
					'checkoutDifferentCurrency'     => true,
					'enableAddressCountryNotice'    => true,
					'addressCountryNotice'          => $notice,
					'forceByAddressCountry'         => false,
					'addressCountryMethod'          => 'billing',
					'reloadPageAfterCurrencyChange' => false,
				)
			);
			$string_translations['psmmc-address-country-notice'] = $notice;
			update_option( 'psmmc-string-translation', $string_translations );
		}

		/**
		 * Reset display settings
		 *
		 * @return void
		 */
		public static function reset_display_options() {

			update_option(
				'psmwoo_mc_display_options',
				array(
					'enableSingleProduct'    => true,
					'singleProductPosition'  => 'after-short-descriptoion',
					'switcherSize'           => 'medium',
					'switcherFlag'           => true,
					'switcherCurrencyName'   => true,
					'switcherCurrencySymbol' => true,
					'switcherCurrencyCode'   => true,
				)
			);
		}

		/**
		 * Reset advanced settings
		 *
		 * @return void
		 */
		public static function reset_advanced_settings() {

			update_option(
				'psmwoo_mc_advanced_settings',
				array(
					'currencyExchangeRateApi'         => 'yahoo',
					'currencyExchangeRateApiKey'      => '',
					'enableAutoUpdateExchangeRates'   => false,
					'autoUpdateExchangeRatesInterval' => 1,
					'autoUpdateExchangeRatesUnit'     => 'hour',
					'enableAutoSelectCountryCurrency' => false,
					'geoapi'                          => 'ipinfo',
					'enableCachePlugin'               => false,
				)
			);
		}

		/**
		 * Deactivate the plugin
		 *
		 * @return void
		 */
		public static function deactivate() {

			do_action( 'psmwoo_mc_deactivate' );
		}
	}
endif;

PSMWOO_MC_Installation::init();
