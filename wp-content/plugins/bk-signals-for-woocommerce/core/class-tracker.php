<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Tracker {

    public static function init() {
        // Views.
        add_action( 'template_redirect', [ __CLASS__, 'track_product_view' ] );

        // Carts.
        add_action( 'woocommerce_add_to_cart', [ __CLASS__, 'track_add_to_cart' ], 10, 6 );
        add_action( 'woocommerce_after_cart_item_quantity_update', [ __CLASS__, 'sync_active_cart' ], 20, 0 );
        add_action( 'woocommerce_cart_item_removed', [ __CLASS__, 'sync_active_cart' ], 20, 0 );
        add_action( 'woocommerce_cart_item_restored', [ __CLASS__, 'sync_active_cart' ], 20, 0 );
        add_action( 'woocommerce_cart_emptied', [ __CLASS__, 'sync_active_cart' ], 20, 0 );

        // Orders.
        add_action( 'woocommerce_payment_complete', [ __CLASS__, 'track_order_paid' ] );
        add_action( 'woocommerce_order_status_processing', [ __CLASS__, 'track_order_paid' ] );
        add_action( 'woocommerce_order_status_completed', [ __CLASS__, 'track_order_paid' ] );
        add_action( 'woocommerce_order_status_changed', [ __CLASS__, 'sync_order_status' ], 10, 4 );
        add_action( 'woocommerce_order_status_cancelled', [ __CLASS__, 'void_order_sales' ] );
        add_action( 'woocommerce_order_status_failed', [ __CLASS__, 'void_order_sales' ] );
        add_action( 'woocommerce_order_status_refunded', [ __CLASS__, 'void_order_sales' ] );
        add_action( 'woocommerce_before_delete_order', [ __CLASS__, 'void_order_sales' ] );
        add_action( 'woocommerce_before_trash_order', [ __CLASS__, 'void_order_sales' ] );
        add_action( 'before_delete_post', [ __CLASS__, 'void_order_post' ] );
        add_action( 'wp_trash_post', [ __CLASS__, 'void_order_post' ] );

        // Live visitors.
    }

    public static function track_product_view() {
        if ( ! is_product() ) {
            return;
        }

        // Admin test mode.
        $track_admins = get_option( 'bksignals_track_admins', '0' );
        if ( ! BKSignals_Helpers::is_bot() ) {
            if ( BKSignals_Helpers::is_admin_user() && '1' !== $track_admins ) {
                return;
            }
        } else {
            return;
        }

        $product_id  = get_queried_object_id();
        if ( ! $product_id ) {
            return;
        }

        // Visitor.
        $visitor_key = BKSignals_Helpers::get_visitor_id();

        // Daily limit.
        $transient_key = 'bksignals_vq_' . $product_id . '_' . md5( $visitor_key );
        if ( false !== get_transient( $transient_key ) ) {
            return;
        }
        set_transient( $transient_key, 1, DAY_IN_SECONDS );

        BKSignals_Database::queue_view( $product_id, md5( $visitor_key ) );
    }

    public static function track_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
        if ( BKSignals_Helpers::is_bot() || BKSignals_Helpers::is_admin_user() ) {
            return;
        }

        // Product ID.
        $pid = $variation_id ? $variation_id : $product_id;
        BKSignals_Database::increment_cart( $pid );
        self::sync_active_cart();
    }

    public static function sync_active_cart() {
        if ( BKSignals_Helpers::is_bot() ) {
            return;
        }

        $visitor_id = BKSignals_Helpers::get_visitor_id();

        if ( BKSignals_Helpers::is_admin_user() ) {
            BKSignals_Database::replace_active_cart( $visitor_id, [] );
            return;
        }

        if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
            return;
        }

        $items = [];

        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $pid = ! empty( $cart_item['product_id'] ) ? absint( $cart_item['product_id'] ) : 0;
            $qty = ! empty( $cart_item['quantity'] ) ? absint( $cart_item['quantity'] ) : 0;

            if ( ! $pid || ! $qty ) {
                continue;
            }

            $items[ $pid ] = ( $items[ $pid ] ?? 0 ) + $qty;
        }

        BKSignals_Database::replace_active_cart( $visitor_id, $items );
    }

    public static function track_order_paid( $order_id ) {
        if ( BKSignals_Helpers::is_bot() ) {
            return;
        }

        $order = function_exists( 'wc_get_order' ) ? wc_get_order( $order_id ) : null;
        if ( ! $order ) {
            return;
        }

        if ( self::is_internal_order( $order ) ) {
            BKSignals_Database::void_order_sales( $order_id );
            return;
        }

        $current_filter = current_filter();
        $paid_filters = [
            'woocommerce_payment_complete',
            'woocommerce_order_status_processing',
            'woocommerce_order_status_completed',
        ];

        if ( ! in_array( $current_filter, $paid_filters, true ) ) {
            return;
        }

        BKSignals_Database::record_order_sales( $order_id );
    }

    public static function void_order_sales( $order_id ) {
        if ( is_object( $order_id ) && method_exists( $order_id, 'get_id' ) ) {
            $order_id = $order_id->get_id();
        }

        BKSignals_Database::void_order_sales( absint( $order_id ) );
    }

    public static function sync_order_status( $order_id, $old_status, $new_status, $order ) {
        if ( in_array( $new_status, [ 'processing', 'completed' ], true ) ) {
            if ( $order && self::is_internal_order( $order ) ) {
                BKSignals_Database::void_order_sales( absint( $order_id ) );
                return;
            }

            BKSignals_Database::record_order_sales( absint( $order_id ) );
            return;
        }

        BKSignals_Database::void_order_sales( absint( $order_id ) );
    }

    public static function void_order_post( $post_id ) {
        if ( ! function_exists( 'wc_get_order' ) ) {
            return;
        }

        $order = wc_get_order( $post_id );
        if ( ! $order ) {
            return;
        }

        BKSignals_Database::void_order_sales( absint( $post_id ) );
    }

    private static function is_internal_order( $order ): bool {
        if ( 'admin' === $order->get_created_via() ) {
            return true;
        }

        $customer_id = absint( $order->get_customer_id() );
        if ( $customer_id && ( user_can( $customer_id, 'manage_woocommerce' ) || user_can( $customer_id, 'manage_options' ) ) ) {
            return true;
        }

        return false;
    }
}
