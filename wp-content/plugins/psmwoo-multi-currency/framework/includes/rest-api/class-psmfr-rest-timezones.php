<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMFR_Rest_Timezones {

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
			'/timezones',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_timezones' ),
				// This endpoint is intentionally public. Only public timezone data is returned.
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Retrives the timezones.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response
	 */
	public static function get_timezones( $request ) {

		$tzstring = self::get_tz_string();
		$continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

		// Load translations for continents and cities.
		$locale = $request->get_param( 'locale' );
		$mofile = WP_LANG_DIR . '/continents-cities-' . $locale . '.mo';
		unload_textdomain( 'continents-cities', true );
		load_textdomain( 'continents-cities', $mofile, $locale );

		$tz_identifiers = timezone_identifiers_list();
		$zonen = array();

		foreach ( $tz_identifiers as $zone ) {
			$zone = explode( '/', $zone );
			if ( ! in_array( $zone[0], $continents, true ) ) {
				continue;
			}

			// This determines what gets set and translated - we don't translate Etc/* strings here, they are done later.
			$exists    = array(
				0 => ( isset( $zone[0] ) && $zone[0] ),
				1 => ( isset( $zone[1] ) && $zone[1] ),
				2 => ( isset( $zone[2] ) && $zone[2] ),
			);
			$exists[3] = ( $exists[0] && 'Etc' !== $zone[0] );
			$exists[4] = ( $exists[1] && $exists[3] );
			$exists[5] = ( $exists[2] && $exists[3] );

			// phpcs:disable WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
			$zonen[] = array(
				'continent'   => ( $exists[0] ? $zone[0] : '' ),
				'city'        => ( $exists[1] ? $zone[1] : '' ),
				'subcity'     => ( $exists[2] ? $zone[2] : '' ),
				't_continent' => ( $exists[3] ? translate( str_replace( '_', ' ', $zone[0] ), 'continents-cities' ) : '' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
				't_city'      => ( $exists[4] ? translate( str_replace( '_', ' ', $zone[1] ), 'continents-cities' ) : '' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
				't_subcity'   => ( $exists[5] ? translate( str_replace( '_', ' ', $zone[2] ), 'continents-cities' ) : '' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
			);
			// phpcs:enable
		}
		usort( $zonen, '_wp_timezone_choice_usort_callback' );

		$timezones = array();
		$current_tz = array();
		foreach ( $zonen as $key => $zone ) {
			$value = array( $zone['continent'], $zone['city'] );
			$label = array( $zone['t_continent'], $zone['t_city'] );
			if ( ! empty( $zone['subcity'] ) ) {
				$value[] = $zone['subcity'];
				$label[] = $zone['t_subcity'];
			}

			$value = implode( '/', $value );
			$timezones[] = array(
				'value'    => $value,
				'label'    => implode( '/', $label ),
				'selected' => $tzstring === $value,
			);
		}

		// Do UTC.
		$timezones[] = array(
			'value'    => 'UTC',
			'label'    => 'UTC',
			'selected' => $tzstring === 'UTC',
		);
		$offset_range = array(
			-12,
			-11.5,
			-11,
			-10.5,
			-10,
			-9.5,
			-9,
			-8.5,
			-8,
			-7.5,
			-7,
			-6.5,
			-6,
			-5.5,
			-5,
			-4.5,
			-4,
			-3.5,
			-3,
			-2.5,
			-2,
			-1.5,
			-1,
			-0.5,
			0,
			0.5,
			1,
			1.5,
			2,
			2.5,
			3,
			3.5,
			4,
			4.5,
			5,
			5.5,
			5.75,
			6,
			6.5,
			7,
			7.5,
			8,
			8.5,
			8.75,
			9,
			9.5,
			10,
			10.5,
			11,
			11.5,
			12,
			12.75,
			13,
			13.75,
			14,
		);
		foreach ( $offset_range as $offset ) {
			if ( 0 <= $offset ) {
				$offset_name = '+' . $offset;
			} else {
				$offset_name = (string) $offset;
			}

			$offset_value = $offset_name;
			$offset_name  = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
			$offset_name  = 'UTC' . $offset_name;
			$offset_value = 'UTC' . $offset_value;
			$timezones[] = array(
				'value'    => $offset_value,
				'label'    => $offset_name,
				'selected' => $tzstring === $offset_value,
			);
		}

		return rest_ensure_response( $timezones );
	}

	/**
	 * Get the timezone string.
	 *
	 * @return string
	 */
	private static function get_tz_string() {
		$current_offset = get_option( 'gmt_offset' );
		$tzstring = get_option( 'timezone_string' );

		// Remove old Etc mappings. Fallback to gmt_offset.
		if ( str_contains( $tzstring, 'Etc/GMT' ) ) {
			$tzstring = '';
		}

		if ( empty( $tzstring ) ) { // Create a UTC+- zone if no timezone string exists.
			if ( 0 == $current_offset ) {
				$tzstring = 'UTC+0';
			} elseif ( $current_offset < 0 ) {
				$tzstring = 'UTC' . $current_offset;
			} else {
				$tzstring = 'UTC+' . $current_offset;
			}
		}

		return $tzstring;
	}
}

PSMFR_Rest_Timezones::init();
