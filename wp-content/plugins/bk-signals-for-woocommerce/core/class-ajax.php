<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Ajax {

    public static function init() {
        add_action( 'wp_ajax_bksignals_live_heartbeat',        [ __CLASS__, 'live_heartbeat' ] );
        add_action( 'wp_ajax_nopriv_bksignals_live_heartbeat', [ __CLASS__, 'live_heartbeat' ] );

        add_action( 'wp_ajax_bksignals_live_leave',        [ __CLASS__, 'live_leave' ] );
        add_action( 'wp_ajax_nopriv_bksignals_live_leave', [ __CLASS__, 'live_leave' ] );

        add_action( 'wp_ajax_bksignals_get_live_count',        [ __CLASS__, 'get_live_count' ] );
        add_action( 'wp_ajax_nopriv_bksignals_get_live_count', [ __CLASS__, 'get_live_count' ] );

        add_action( 'wp_ajax_bksignals_get_recent_sale',        [ __CLASS__, 'get_recent_sale' ] );
        add_action( 'wp_ajax_nopriv_bksignals_get_recent_sale', [ __CLASS__, 'get_recent_sale' ] );
    }

    // Live heartbeat.
    public static function live_heartbeat() {
        if ( ! check_ajax_referer( 'bksignals_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'code' => 'invalid_nonce' ], 403 );
        }

        $product_id = absint( wp_unslash( $_POST['product_id'] ?? 0 ) );
        if ( ! $product_id ) {
            wp_send_json_error( [ 'code' => 'no_product_id' ] );
        }

        $track_admins = get_option( 'bksignals_track_admins', '0' );
        $should_track = ! BKSignals_Helpers::is_bot()
            && ( '1' === $track_admins || ! BKSignals_Helpers::is_admin_user() );

        if ( $should_track ) {
            $visitor_id = BKSignals_Helpers::get_visitor_id();
            BKSignals_Database::upsert_live_visitor( $product_id, $visitor_id );
        }

        $count = BKSignals_Database::count_live_visitors( $product_id );
        $stats = BKSignals_Database::get_product_stats( $product_id );
        $active_cart = BKSignals_Database::count_active_carts( $product_id );

        wp_send_json_success( [
            'count'     => $count,
            'active_cart' => $active_cart,
            'views_24h' => (int) ( $stats['views_24h']  ?? 0 ),
            'views_7d'  => (int) ( $stats['views_7d']   ?? 0 ),
            'views_30d' => (int) ( $stats['views_30d']  ?? 0 ),
            'cart_24h'  => (int) ( $stats['cart_24h']   ?? 0 ),
            'cart_7d'   => (int) ( $stats['cart_7d']    ?? 0 ),
            'cart_30d'  => (int) ( $stats['cart_30d']   ?? 0 ),
            'sales_24h' => (int) ( $stats['sales_24h']  ?? 0 ),
            'sales_7d'  => (int) ( $stats['sales_7d']   ?? 0 ),
            'sales_30d' => (int) ( $stats['sales_30d']  ?? 0 ),
        ] );
    }

    // Live count.
    public static function live_leave() {
        if ( ! check_ajax_referer( 'bksignals_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'code' => 'invalid_nonce' ], 403 );
        }

        $product_id = absint( wp_unslash( $_POST['product_id'] ?? 0 ) );
        if ( $product_id && ! BKSignals_Helpers::is_bot() ) {
            BKSignals_Database::remove_live_visitor( $product_id, BKSignals_Helpers::get_visitor_id() );
        }

        wp_send_json_success();
    }

    public static function get_live_count() {
        if ( ! check_ajax_referer( 'bksignals_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'code' => 'invalid_nonce' ], 403 );
        }

        $product_id = absint( wp_unslash( $_POST['product_id'] ?? 0 ) );
        if ( ! $product_id ) {
            wp_send_json_error( [ 'code' => 'no_product_id' ] );
        }

        wp_send_json_success( [
            'count' => BKSignals_Database::count_live_visitors( $product_id ),
        ] );
    }

    // Recent sales.
    public static function get_recent_sale() {
        if ( ! check_ajax_referer( 'bksignals_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'code' => 'invalid_nonce' ], 403 );
        }

        $product_id = absint( wp_unslash( $_POST['product_id'] ?? 0 ) );
        $limit = max( 1, min( 50, absint( wp_unslash( $_POST['limit'] ?? get_option( 'bksignals_popup_limit', 10 ) ) ) ) );
        $product_scope = '1' === get_option( 'bksignals_popup_product_scope', '1' );

        $sales = ( $product_id && $product_scope )
            ? BKSignals_Database::get_recent_sales( $product_id, $limit )
            : BKSignals_Database::get_any_recent_sales( $limit );

        if ( empty( $sales ) ) {
            wp_send_json_success( null );
        }

        $items = [];
        foreach ( $sales as $sale ) {
            $customer_name = trim( (string) ( $sale['masked_customer_name'] ?? '' ) );
            if ( '' === $customer_name ) {
                continue;
            }

            $items[] = [
                'product_name'  => esc_html( $sale['product_name'] ?? '' ),
                'customer_name' => esc_html( $customer_name ),
                'city'          => '',
                'time_ago'      => BKSignals_Helpers::human_time_diff_short( $sale['created_at'] ),
                'product_image' => esc_url( $sale['product_image'] ?? '' ),
            ];
        }

        if ( empty( $items ) ) {
            wp_send_json_success( null );
        }

        wp_send_json_success( [
            'sales' => $items,
        ] );
    }
}
