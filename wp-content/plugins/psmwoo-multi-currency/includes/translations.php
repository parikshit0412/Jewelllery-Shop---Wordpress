<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'PSMWOO_MC_Translations' ) ) :

	final class PSMWOO_MC_Translations {

		/**
		 * Get translated string
		 *
		 * @param mixed $name - String index given earlier while adding it to translation.
		 * @param mixed $str - String to be translated.
		 * @return mixed
		 */
		public static function get( $name, $str ) {

			// latest compatible.
			if ( defined( 'WPML_PLUGIN_PATH' ) ) {
				return apply_filters( 'wpml_translate_single_string', $str, 'psmwoo-multi-currency', $name, ICL_LANGUAGE_CODE );  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML core hook, cannot be prefixed.
			}

			if ( function_exists( 'icl_t' ) ) {
				return icl_t( 'psmwoo-multi-currency', $name );
			}

			return $str;
		}

		/**
		 * Adds a string to translation
		 *
		 * @param mixed $name - Unique index for string.
		 * @param mixed $str - String to be translated.
		 * @return void
		 */
		public static function add( $name, $str ) {

			if ( ! $str ) {
				return;
			}

			$strings = get_option( 'psmmc-string-translation', array() );
			if ( isset( $strings[ $name ] ) ) {
				self::remove( $name );
			}

			$strings[ $name ] = $str;
			update_option( 'psmmc-string-translation', $strings );

			// register strings.
			if ( defined( 'WPML_PLUGIN_PATH' ) ) {

				PSMWOO_MC_WPML::register_strings();
			} elseif ( function_exists( 'icl_t' ) ) {

				PSMWOO_MC_Polylang::register_strings();
			}
		}

		/**
		 * Remove string from translation
		 *
		 * @param string $name - translation string name.
		 * @return void
		 */
		public static function remove( $name ) {

			if ( function_exists( 'icl_unregister_string' ) ) {
				icl_unregister_string( 'psmwoo-multi-currency', $name );
			}

			$strings = get_option( 'psmmc-string-translation', array() );
			if ( isset( $strings[ $name ] ) ) {
				unset( $strings[ $name ] );
				update_option( 'psmmc-string-translation', $strings );
			}
		}
	}
endif;
