<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PSMFR_Rest_User_Roles {

	/**
	 * Initialize the class.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'rest_api_callback' ) );
	}

	/**
	 * Registers the REST API routes for the plugin.
	 *
	 * This method is called during the rest_api_init action and is responsible for
	 * registering the REST API routes for the plugin.
	 *
	 * @return void
	 */
	public static function rest_api_callback() {

		register_rest_route(
			'psmfr/v1',
			'/user-roles',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_wp_user_roles' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_woocommerce' );
				},
			)
		);
	}

	/**
	 * Get WordPress user roles.
	 *
	 * @return WP_REST_Response
	 */
	public static function get_wp_user_roles() {

		$roles = wp_roles()->roles;
		$user_roles = array();
		foreach ( $roles as $key => $data ) {
			$user_roles[] = array(
				'label' => isset( $data['name'] ) ? $data['name'] : $key,
				'value' => $key,
			);
		}
		return rest_ensure_response( $user_roles );
	}
}

PSMFR_Rest_User_Roles::init();
