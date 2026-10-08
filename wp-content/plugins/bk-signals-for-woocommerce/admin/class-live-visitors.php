<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Admin_Live_Visitors {

    public static function init() {
        add_action( 'admin_post_bksignals_save_live_visitors', [ __CLASS__, 'save' ] );
    }

    public static function design_options(): array {
        return [
            'minimal'   => __( 'Minimal', 'bk-signals-for-woocommerce' ),
            'modern'    => __( 'Modern', 'bk-signals-for-woocommerce' ),
            'card'      => __( 'Card', 'bk-signals-for-woocommerce' ),
            'badge'     => __( 'Badge', 'bk-signals-for-woocommerce' ),
            'pulse'     => __( 'Pulse', 'bk-signals-for-woocommerce' ),
            'glass'     => __( 'Glass', 'bk-signals-for-woocommerce' ),
            'compact'   => __( 'Compact', 'bk-signals-for-woocommerce' ),
            'ribbon'    => __( 'Ribbon', 'bk-signals-for-woocommerce' ),
            'inline'    => __( 'Inline', 'bk-signals-for-woocommerce' ),
            'spotlight' => __( 'Spotlight', 'bk-signals-for-woocommerce' ),
        ];
    }

    public static function render() {
        $widgets = get_option( 'bksignals_live_visitors_widgets', [] );
        $changed = false;

        foreach ( $widgets as $idx => $widget ) {
            if ( empty( $widget['id'] ) ) {
                $widgets[ $idx ]['id'] = wp_generate_uuid4();
                $changed = true;
            }
            if ( empty( $widget['shortcode_key'] ) ) {
                $widgets[ $idx ]['shortcode_key'] = self::unique_shortcode_key( '', $widgets, (int) $idx );
                $changed = true;
            }
            if ( ! array_key_exists( 'enabled', $widget ) ) {
                $widgets[ $idx ]['enabled'] = true;
                $changed = true;
            }
            if ( empty( $widget['design'] ) || ! array_key_exists( $widget['design'], self::design_options() ) ) {
                $widgets[ $idx ]['design'] = 'minimal';
                $changed = true;
            }
        }

        if ( $changed ) {
            update_option( 'bksignals_live_visitors_widgets', $widgets );
        }

        $edit_index = BKSignals_Helpers::get_admin_edit_index( $widgets, 'bk-signals-live-visitors' );
        $edit_widget = ( null !== $edit_index && isset( $widgets[ $edit_index ] ) ) ? $widgets[ $edit_index ] : null;

        include BKSIGNALS_DIR . 'admin/views/live-visitors.php';
    }

    public static function save() {
        check_admin_referer( 'bksignals_live_visitors_save' );
        if ( ! current_user_can( 'manage_woocommerce' ) ) wp_die( 'Forbidden' );

        $widgets = get_option( 'bksignals_live_visitors_widgets', [] );

        $action = sanitize_text_field( wp_unslash( $_POST['bksignals_action'] ?? 'create' ) );

        if ( 'delete' === $action ) {
            $idx = isset( $_POST['widget_index'] ) ? absint( wp_unslash( $_POST['widget_index'] ) ) : -1;
            if ( isset( $widgets[ $idx ] ) ) {
                unset( $widgets[ $idx ] );
                $widgets = array_values( $widgets );
            }
        } else {
            $idx = isset( $_POST['widget_index'] ) ? absint( wp_unslash( $_POST['widget_index'] ) ) : -1;
            $shortcode_key = self::unique_shortcode_key(
                sanitize_text_field( wp_unslash( $_POST['shortcode_key'] ?? '' ) ),
                $widgets,
                $idx
            );

            $posted_design = isset( $_POST['design'] ) ? sanitize_key( wp_unslash( $_POST['design'] ) ) : 'minimal';

            $widget = [
                'id'            => ( $idx >= 0 && isset( $widgets[ $idx ]['id'] ) ) ? $widgets[ $idx ]['id'] : wp_generate_uuid4(),
                'shortcode_key' => $shortcode_key,
                'name'          => sanitize_text_field( wp_unslash( $_POST['widget_name'] ?? __( 'Live Visitors Widget', 'bk-signals-for-woocommerce' ) ) ),
                'design'        => self::sanitize_design( $posted_design ),
                'enabled' => ! empty( $_POST['enabled'] ),
            ];

            if ( $idx >= 0 && isset( $widgets[ $idx ] ) ) {
                $widgets[ $idx ] = $widget;
            } else {
                $widgets[] = $widget;
            }
        }

        update_option( 'bksignals_live_visitors_widgets', $widgets );
        wp_safe_redirect( BKSignals_Helpers::admin_notice_url( 'bk-signals-live-visitors', 'saved' ) );
        exit;
    }

    public static function get_shortcode( int $index ): string {
        $widgets = get_option( 'bksignals_live_visitors_widgets', [] );
        if ( empty( $widgets[ $index ]['shortcode_key'] ) && empty( $widgets[ $index ]['id'] ) ) {
            return '[bk_signals_live_visitors design="' . esc_attr( $widgets[ $index ]['design'] ?? 'minimal' ) . '"]';
        }

        return '[bk_signals_live_visitors widget="' . esc_attr( $widgets[ $index ]['shortcode_key'] ?? $widgets[ $index ]['id'] ) . '"]';
    }

    private static function normalize_shortcode_key( string $key ): string {
        $key = sanitize_title( $key );
        return trim( $key, '-' );
    }

    private static function sanitize_design( string $design ): string {
        $design = sanitize_key( $design );
        return array_key_exists( $design, self::design_options() ) ? $design : 'minimal';
    }

    private static function unique_shortcode_key( string $key, array $widgets, int $current_index = -1 ): string {
        $base = self::normalize_shortcode_key( $key );

        if ( '' === $base ) {
            $base = 'live-visitors-' . ( count( $widgets ) + 1 );
            if ( $current_index >= 0 ) {
                $base = 'live-visitors-' . ( $current_index + 1 );
            }
        }

        $candidate = $base;
        $suffix = 2;

        while ( self::shortcode_key_exists( $candidate, $widgets, $current_index ) ) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    private static function shortcode_key_exists( string $key, array $widgets, int $current_index ): bool {
        foreach ( $widgets as $idx => $widget ) {
            if ( $idx === $current_index ) {
                continue;
            }
            if ( ! empty( $widget['shortcode_key'] ) && $key === $widget['shortcode_key'] ) {
                return true;
            }
        }
        return false;
    }
}
