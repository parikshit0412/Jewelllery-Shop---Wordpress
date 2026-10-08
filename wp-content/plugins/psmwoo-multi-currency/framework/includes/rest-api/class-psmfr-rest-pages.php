<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMFR_Rest_Pages {

	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );
	}

	/**
	 * Registers the REST API routes.
	 *
	 * @return void
	 */
	public static function register_rest_routes() {
		register_rest_route(
			'psmfr/v1',
			'/pages',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_pages' ),
					'permission_callback' => function () {
						return current_user_can( 'manage_woocommerce' );
					},
				),
			),
		);
	}

	/**
	 * Retrieves the pages.
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return WP_REST_Response
	 */
	public static function get_pages( $request ) {

		// Get and sanitize search parameter if present.
		$search = isset( $request['s'] ) ? sanitize_text_field( $request['s'] ) : '';

		// Query all published pages, with optional search.
		$args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
		);
		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		$query = new WP_Query( $args );
		$all_pages = $query->posts;

		$pages = array();

		if ( defined( 'ICL_SITEPRESS_VERSION' ) && function_exists( 'wpml_get_default_language' ) ) {
			// WPML support.
			$default_lang = apply_filters( 'wpml_default_language', null ); //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML core hook, do not prefix
			foreach ( $all_pages as $page ) {
				$lang = apply_filters(
					'wpml_element_language_code', //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML core hook, do not prefix
					null,
					array(
						'element_id'   => $page->ID,
						'element_type' => 'post_page',
					)
				);
				if ( $lang === $default_lang ) {
					$pages[] = self::format_page_response( $page );
				}
			}
		} elseif ( function_exists( 'pll_default_language' ) && function_exists( 'pll_get_post_language' ) ) {
			// Polylang support.
			$default_lang = pll_default_language();
			foreach ( $all_pages as $page ) {
				$lang = pll_get_post_language( $page->ID );
				if ( $lang === $default_lang ) {
					$pages[] = self::format_page_response( $page );
				}
			}
		} else {
			// No multi-lingual plugin, return all pages.
			foreach ( $all_pages as $page ) {
				$pages[] = self::format_page_response( $page );
			}
		}

		return rest_ensure_response( $pages );
	}

	/**
	 * Format a page object for API response.
	 *
	 * @param WP_Post $page - The page object.
	 * @return array
	 */
	private static function format_page_response( $page ) {
		return array(
			'label' => get_the_title( $page->ID ),
			'value' => $page->ID,
		);
	}
}

PSMFR_Rest_Pages::init();
