<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'PSMFR_Installation' ) ) :

	final class PSMFR_Installation {

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
				if ( 'yes' === get_transient( 'psmfr_installing' ) ) {
					return;
				}

				// Set transient.
				set_transient( 'psmfr_installing', 'yes', MINUTE_IN_SECONDS * 10 );

				// Run installation.
				if ( self::$current_version == 0 ) {
					add_action( 'init', array( __CLASS__, 'initial_setup' ), 1 );
				} else {
					add_action( 'init', array( __CLASS__, 'upgrade' ), 1 );
				}

				// Delete transient.
				delete_transient( 'psmfr_installing' );
			}
		}

		/**
		 * Check version
		 */
		public static function get_current_version() {

			self::$current_version = get_option( 'psmfr_current_version', 0 );
		}

		/**
		 * Check for upgrade
		 */
		public static function check_upgrade() {

			if ( self::$current_version != PSMFR_VERSION ) {
				self::$is_upgrade = true;
			}
		}

		/**
		 * First time installation
		 */
		public static function initial_setup() {

			self::reset_psmfr_settings();
			self::set_upgrade_complete();
		}

		/**
		 * Upgrade the version
		 */
		public static function upgrade() {

			self::set_upgrade_complete();
		}

		/**
		 * Mark upgrade as complete
		 */
		public static function set_upgrade_complete() {

			update_option( 'psmfr_current_version', PSMFR_VERSION );
			self::$current_version = PSMFR_VERSION;
			self::$is_upgrade      = false;
		}

		/**
		 * Reset PSMFR settings
		 *
		 * @return Array
		 */
		public static function reset_psmfr_settings() {

			$settings = array(
				'primaryColor' => '#0a2540',
				'linkColor'    => '#2271B1',
				'dangerColor'  => '#CF1322',
				'successColor' => '#389E0D',
				'warningColor' => '#D48806',
				'popupZIndex'  => 999999,
			);
			update_option( 'psmfr_settings', $settings );
			return $settings;
		}
	}
endif;

PSMFR_Installation::init();
