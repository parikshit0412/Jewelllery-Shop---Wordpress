<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMFR_Frontend {

	/**
	 * Initializes the Psmfr_Frontend class.
	 *
	 * This method is called when the plugin is loaded and is responsible for executing
	 * any necessary code.
	 *
	 * @return void
	 */
	public static function init() {

		// Enqueue the scripts and styles for the frontend.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		// add the framework object to the global scope.
		add_action( 'wp_enqueue_scripts', array( 'PSMFR_Helper', 'render_framework_js_object' ) );
	}

	/**
	 * Enqueues the scripts and styles for the frontend.
	 *
	 * This method is called during the wp_enqueue_scripts action and is responsible
	 * for enqueuing the necessary scripts and styles for the frontend.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {

		// This is dummy css file loaded on every page. Main purpose is to load global inline styles using "psmfr" handle.
		wp_enqueue_style(
			'psmfr',
			PSMFR_PLUGIN_URL . 'framework/index.css',
			array(),
			'1.0.0'
		);
	}
}

PSMFR_Frontend::init();
