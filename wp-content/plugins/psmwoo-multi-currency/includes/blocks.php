<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Blocks {

	/**
	 * Register blocks
	 *
	 * @return void
	 */
	public static function init() {

		// add gutenberg block.
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );

		// add elementor widget.
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_new_widgets' ) );
	}

	/**
	 * Register gutenberg block
	 *
	 * @return void
	 */
	public static function register_blocks() {

		$blocks_dir = PSMWOO_MC_ABSPATH . 'build/blocks';
		$blocks = array_diff( scandir( $blocks_dir ), array( '.', '..' ) );
		foreach ( $blocks as $block ) {
			$block_path = $blocks_dir . '/' . $block;
			if ( is_dir( $block_path ) ) {
				register_block_type( $block_path );
			}
		}
	}

	/**
	 * Register elementor widget
	 *
	 * @param object $manager Elementor manager.
	 * @return void
	 */
	public static function register_new_widgets( $manager ) {
		require_once __DIR__ . '/elementor/switcher.php';
		$manager->register( new PSMWOO_MC_Elementor_Widget() );
	}
}

PSMWOO_MC_Blocks::init();
