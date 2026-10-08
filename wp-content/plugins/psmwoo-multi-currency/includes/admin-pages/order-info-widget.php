<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Order_Info_Widget {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_order_info_meta_boxes' ), 10, 2 );
	}

	/**
	 * Add the order info meta boxes.
	 *
	 * @param string $post_type The post type.
	 * @param object $post The post object.
	 * @return void
	 */
	public static function add_order_info_meta_boxes( $post_type, $post ) {

		$screen = get_current_screen();
		if ( ! ( 'woocommerce_page_wc-orders' == $screen->id || 'shop_order' == $screen->id ) && 'edit' !== $screen->action ) {
			return;
		}

		add_meta_box(
			'psmwmc-order-info',
			__( 'Currency', 'psmwoo-multi-currency' ),
			array( __CLASS__, 'add_order_info_data_meta_boxes' ),
			$screen->id,
			'side',
			'core'
		);
	}

	/**
	 * Add the order info data meta boxes.
	 *
	 * @param object $order The post object.
	 * @return void
	 */
	public static function add_order_info_data_meta_boxes( $order ) {

		if ( $order instanceof WP_Post ) {
			$order = wc_get_order( $order->ID );
		}

		if ( ! $order->get_id() ) {
			return;
		}

		$exchange_rate = $order->get_meta( '_psmwoo_mc_currency_rate' );
		$base_currency = $order->get_meta( '_psmwoo_mc_base_currency' );
		?>
		<div class="inside">
			<div class="customer-history order-attribution-metabox">
				<h4><?php echo esc_html__( 'Order Currency', 'psmwoo-multi-currency' ); ?> : </h4><span><?php echo esc_html( $order->get_currency() ); ?></span>
				<h4><?php echo esc_html__( 'Base Currency', 'psmwoo-multi-currency' ); ?> : </h4><span><?php echo esc_html( $base_currency ); ?></span>
				<h4><?php echo esc_html__( 'Exchange Rate', 'psmwoo-multi-currency' ); ?> : </h4><span><?php echo esc_html( $exchange_rate ); ?></span>
			</div>
		</div>
		<?php
	}
}

PSMWOO_MC_Order_Info_Widget::init();
