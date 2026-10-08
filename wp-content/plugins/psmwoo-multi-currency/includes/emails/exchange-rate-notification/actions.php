<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PSMWOO_MC_Email_Actions {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		// Load the custom WooCommerce email class.
		add_filter( 'woocommerce_email_classes', array( __CLASS__, 'psmwoomc_register_custom_email_class' ) );
	}

	/**
	 * Register the custom WooCommerce email class.
	 *
	 * @param array $emails - array of WooCommerce email classes.
	 * @return array
	 */
	public static function psmwoomc_register_custom_email_class( $emails ) {
		require_once plugin_dir_path( __FILE__ ) . 'email.php';
		$object = new PSMWOO_MC_Exchange_Rate_Notification();
		$emails['PSMWOO_MC_Exchange_Rate_Notification'] = $object;
		return $emails;
	}
}
PSMWOO_MC_Email_Actions::init();
