<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Assets {

    public static function init() {
        add_action( 'wp_enqueue_scripts',    [ __CLASS__, 'frontend_assets' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_assets' ] );
    }

    public static function frontend_assets() {
        if ( ! is_product() && ! is_shop() && ! is_product_category() ) {
            // Popup pages.
            if ( ! get_option( 'bksignals_popup_enabled', '1' ) ) return;
        }

        wp_enqueue_style(
            'bk-signals-frontend',
            BKSIGNALS_URL . 'assets/css/frontend.css',
            [],
            BKSIGNALS_VERSION
        );

        wp_enqueue_script(
            'bk-signals-frontend',
            BKSIGNALS_URL . 'assets/js/frontend.js',
            [ 'jquery' ],
            BKSIGNALS_VERSION,
            true
        );

        // Product context.
        $product_id = is_product() ? get_queried_object_id() : 0;

        $heartbeat_seconds = (int) get_option( 'bksignals_live_refresh_seconds', 30 );
        $heartbeat_seconds = max( 5, min( 300, $heartbeat_seconds ) );

        wp_localize_script( 'bk-signals-frontend', 'BKSignals', [
            'ajax_url'       => admin_url( 'admin-ajax.php' ),
            'nonce'          => wp_create_nonce( 'bksignals_nonce' ),
            'product_id'     => $product_id,
            'heartbeat_ms'   => $heartbeat_seconds * 1000,
            'popup_delay'    => (int) get_option( 'bksignals_popup_delay', 5 ) * 1000,
            'popup_duration' => (int) get_option( 'bksignals_popup_duration', 4 ) * 1000,
            'popup_interval' => (int) get_option( 'bksignals_popup_interval', 8 ) * 1000,
            'popup_limit'    => max( 1, min( 50, (int) get_option( 'bksignals_popup_limit', 10 ) ) ),
            'popup_enabled'  => (bool) get_option( 'bksignals_popup_enabled', '1' ),
            'popup_product_scope' => (bool) get_option( 'bksignals_popup_product_scope', '1' ),
            'popup_mobile_enabled' => (bool) get_option( 'bksignals_popup_mobile_enabled', '1' ),
            'popup_mobile_offset' => max( 0, min( 240, (int) get_option( 'bksignals_popup_mobile_offset', 72 ) ) ),
            'popup_mobile_position' => get_option( 'bksignals_popup_mobile_position', 'bottom-right' ),
            'popup_position' => get_option( 'bksignals_popup_position', 'bottom-left' ),
            'popup_design'   => get_option( 'bksignals_popup_design', 'toast' ),
        ] );
    }

    public static function admin_assets( $hook ) {
        if ( false === strpos( $hook, 'bk-signals' ) ) return;

        wp_enqueue_style(
            'bk-signals-admin',
            BKSIGNALS_URL . 'assets/css/admin.css',
            [],
            BKSIGNALS_VERSION
        );

        wp_enqueue_script(
            'bk-signals-admin',
            BKSIGNALS_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            BKSIGNALS_VERSION,
            true
        );

        wp_localize_script( 'bk-signals-admin', 'BKSignalsAdmin', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'bksignals_admin_nonce' ),
        ] );
    }
}
