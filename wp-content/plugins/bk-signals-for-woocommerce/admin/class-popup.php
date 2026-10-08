<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Admin_Popup {

    public static function init() {
        add_action( 'admin_post_bksignals_save_popup', [ __CLASS__, 'save' ] );
    }

    public static function design_options(): array {
        return [
            'toast'     => __( 'Toast', 'bk-signals-for-woocommerce' ),
            'card'      => __( 'Card', 'bk-signals-for-woocommerce' ),
            'minimal'   => __( 'Minimal', 'bk-signals-for-woocommerce' ),
            'compact'   => __( 'Compact', 'bk-signals-for-woocommerce' ),
            'glass'     => __( 'Glass', 'bk-signals-for-woocommerce' ),
            'spotlight' => __( 'Spotlight', 'bk-signals-for-woocommerce' ),
            'split'     => __( 'Split', 'bk-signals-for-woocommerce' ),
            'ribbon'    => __( 'Ribbon', 'bk-signals-for-woocommerce' ),
            'timeline'  => __( 'Timeline', 'bk-signals-for-woocommerce' ),
            'signature'   => __( 'Signature', 'bk-signals-for-woocommerce' ),
        ];
    }

    public static function position_options(): array {
        return [
            'bottom-left'   => __( 'Bottom Left', 'bk-signals-for-woocommerce' ),
            'bottom-right'  => __( 'Bottom Right', 'bk-signals-for-woocommerce' ),
            'top-left'      => __( 'Top Left', 'bk-signals-for-woocommerce' ),
            'top-right'     => __( 'Top Right', 'bk-signals-for-woocommerce' ),
            'bottom-center' => __( 'Bottom Center', 'bk-signals-for-woocommerce' ),
            'top-center'    => __( 'Top Center', 'bk-signals-for-woocommerce' ),
        ];
    }

    public static function mobile_position_options(): array {
        return [
            'bottom-right'  => __( 'Bottom Right', 'bk-signals-for-woocommerce' ),
            'bottom-left'   => __( 'Bottom Left', 'bk-signals-for-woocommerce' ),
            'bottom-center' => __( 'Bottom Center', 'bk-signals-for-woocommerce' ),
            'top-right'     => __( 'Top Right', 'bk-signals-for-woocommerce' ),
            'top-left'      => __( 'Top Left', 'bk-signals-for-woocommerce' ),
            'top-center'    => __( 'Top Center', 'bk-signals-for-woocommerce' ),
        ];
    }

    public static function render() {
        $saved_design = self::sanitize_design( get_option( 'bksignals_popup_design', 'toast' ) );
        $settings = [
            'enabled'       => get_option( 'bksignals_popup_enabled', '1' ),
            'delay'         => get_option( 'bksignals_popup_delay', '5' ),
            'duration'      => get_option( 'bksignals_popup_duration', '4' ),
            'interval'      => get_option( 'bksignals_popup_interval', '8' ),
            'limit'         => get_option( 'bksignals_popup_limit', '10' ),
            'product_scope' => get_option( 'bksignals_popup_product_scope', '1' ),
            'mobile_enabled' => get_option( 'bksignals_popup_mobile_enabled', '1' ),
            'mobile_offset' => get_option( 'bksignals_popup_mobile_offset', '72' ),
            'mobile_position' => get_option( 'bksignals_popup_mobile_position', 'bottom-right' ),
            'design'        => $saved_design,
            'position'      => get_option( 'bksignals_popup_position', 'bottom-left' ),
        ];
        include BKSIGNALS_DIR . 'admin/views/popup.php';
    }

    public static function save() {
        check_admin_referer( 'bksignals_popup_save' );
        if ( ! current_user_can( 'manage_woocommerce' ) ) wp_die( 'Forbidden' );

        $design = self::sanitize_design( sanitize_key( wp_unslash( $_POST['design'] ?? 'toast' ) ) );

        $position = sanitize_key( wp_unslash( $_POST['position'] ?? 'bottom-left' ) );
        if ( ! array_key_exists( $position, self::position_options() ) ) {
            $position = 'bottom-left';
        }

        $mobile_position = sanitize_key( wp_unslash( $_POST['mobile_position'] ?? 'bottom-right' ) );
        if ( ! array_key_exists( $mobile_position, self::mobile_position_options() ) ) {
            $mobile_position = 'bottom-right';
        }

        update_option( 'bksignals_popup_enabled', ! empty( $_POST['enabled'] ) ? '1' : '0' );
        update_option( 'bksignals_popup_delay', max( 0, min( 300, absint( wp_unslash( $_POST['delay'] ?? 5 ) ) ) ) );
        update_option( 'bksignals_popup_duration', max( 2, min( 60, absint( wp_unslash( $_POST['duration'] ?? 4 ) ) ) ) );
        update_option( 'bksignals_popup_interval', max( 5, min( 300, absint( wp_unslash( $_POST['interval'] ?? 8 ) ) ) ) );
        update_option( 'bksignals_popup_limit', max( 1, min( 50, absint( wp_unslash( $_POST['limit'] ?? 10 ) ) ) ) );
        update_option( 'bksignals_popup_product_scope', ! empty( $_POST['product_scope'] ) ? '1' : '0' );
        update_option( 'bksignals_popup_mobile_enabled', ! empty( $_POST['mobile_enabled'] ) ? '1' : '0' );
        update_option( 'bksignals_popup_mobile_offset', max( 0, min( 240, absint( wp_unslash( $_POST['mobile_offset'] ?? 72 ) ) ) ) );
        update_option( 'bksignals_popup_mobile_position', $mobile_position );
        update_option( 'bksignals_popup_design', $design );
        update_option( 'bksignals_popup_position', $position );

        wp_safe_redirect( BKSignals_Helpers::admin_notice_url( 'bk-signals-popup', 'saved' ) );
        exit;
    }

    private static function sanitize_design( string $design ): string {
        $design = sanitize_key( $design );
        return array_key_exists( $design, self::design_options() ) ? $design : 'toast';
    }
}
