<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Admin_Cart {

    public static function init() {
        add_action( 'admin_post_bksignals_save_cart', [ __CLASS__, 'save' ] );
    }

    public static function design_options(): array {
        return [
            'minimal'     => __( 'Minimal', 'bk-signals-for-woocommerce' ),
            'modern'      => __( 'Modern', 'bk-signals-for-woocommerce' ),
            'card'        => __( 'Card', 'bk-signals-for-woocommerce' ),
            'badge'       => __( 'Badge', 'bk-signals-for-woocommerce' ),
            'metric-card' => __( 'Metric Card', 'bk-signals-for-woocommerce' ),
            'split'       => __( 'Split', 'bk-signals-for-woocommerce' ),
            'pill'        => __( 'Pill', 'bk-signals-for-woocommerce' ),
            'trend'       => __( 'Trend', 'bk-signals-for-woocommerce' ),
            'spotlight'   => __( 'Spotlight', 'bk-signals-for-woocommerce' ),
            'clean'       => __( 'Clean', 'bk-signals-for-woocommerce' ),
        ];
    }

    public static function window_options(): array {
        return [
            1   => __( 'Last 24 Hours', 'bk-signals-for-woocommerce' ),
            7   => __( 'Last 7 Days', 'bk-signals-for-woocommerce' ),
            10  => __( 'Last 10 Days', 'bk-signals-for-woocommerce' ),
            30  => __( 'Last 30 Days', 'bk-signals-for-woocommerce' ),
            90  => __( 'Last 90 Days', 'bk-signals-for-woocommerce' ),
            999 => __( 'All Time', 'bk-signals-for-woocommerce' ),
        ];
    }

    public static function render() {
        $widgets = get_option( 'bksignals_cart_widgets', [] );
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
            $window = self::sanitize_window( $widget['window'] ?? 7 );
            if ( (int) ( $widget['window'] ?? 0 ) !== $window ) {
                $widgets[ $idx ]['window'] = $window;
                $changed = true;
            }
        }

        if ( $changed ) {
            update_option( 'bksignals_cart_widgets', $widgets );
        }

        $edit_index  = BKSignals_Helpers::get_admin_edit_index( $widgets, 'bk-signals-cart' );
        $edit_widget = ( null !== $edit_index && isset( $widgets[ $edit_index ] ) ) ? $widgets[ $edit_index ] : null;

        include BKSIGNALS_DIR . 'admin/views/cart.php';
    }

    public static function save() {
        check_admin_referer( 'bksignals_cart_save' );
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( 'Forbidden' );
        }

        $widgets = get_option( 'bksignals_cart_widgets', [] );
        $action  = sanitize_text_field( wp_unslash( $_POST['bksignals_action'] ?? 'create' ) );

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

            $widget = [
                'id'            => ( $idx >= 0 && isset( $widgets[ $idx ]['id'] ) ) ? $widgets[ $idx ]['id'] : wp_generate_uuid4(),
                'shortcode_key' => $shortcode_key,
                'name'          => sanitize_text_field( wp_unslash( $_POST['widget_name'] ?? __( 'Add To Cart Widget', 'bk-signals-for-woocommerce' ) ) ),
                'design'        => self::sanitize_design( sanitize_key( wp_unslash( $_POST['design'] ?? 'minimal' ) ) ),
                'window'        => self::sanitize_window( absint( wp_unslash( $_POST['window'] ?? 7 ) ) ),
                'enabled'       => ! empty( $_POST['enabled'] ),
            ];

            if ( $idx >= 0 && isset( $widgets[ $idx ] ) ) {
                $widgets[ $idx ] = $widget;
            } else {
                $widgets[] = $widget;
            }
        }

        update_option( 'bksignals_cart_widgets', $widgets );
        wp_safe_redirect( BKSignals_Helpers::admin_notice_url( 'bk-signals-cart', 'saved' ) );
        exit;
    }

    public static function get_shortcode( int $index ): string {
        $widgets = get_option( 'bksignals_cart_widgets', [] );
        if ( empty( $widgets[ $index ]['shortcode_key'] ) && empty( $widgets[ $index ]['id'] ) ) {
            return '[bk_signals_add_to_cart design="' . esc_attr( $widgets[ $index ]['design'] ?? 'minimal' ) . '" window="' . (int) ( $widgets[ $index ]['window'] ?? 7 ) . '"]';
        }

        return '[bk_signals_add_to_cart widget="' . esc_attr( $widgets[ $index ]['shortcode_key'] ?? $widgets[ $index ]['id'] ) . '"]';
    }

    private static function normalize_shortcode_key( string $key ): string {
        $key = sanitize_title( $key );
        return trim( $key, '-' );
    }

    private static function sanitize_design( string $design ): string {
        $design = sanitize_key( $design );
        return array_key_exists( $design, self::design_options() ) ? $design : 'minimal';
    }

    private static function sanitize_window( $window ): int {
        $window = absint( $window );
        return array_key_exists( $window, self::window_options() ) ? $window : 7;
    }

    private static function unique_shortcode_key( string $key, array $widgets, int $current_index = -1 ): string {
        $base = self::normalize_shortcode_key( $key );

        if ( '' === $base ) {
            $base = 'cart-' . ( count( $widgets ) + 1 );
            if ( $current_index >= 0 ) {
                $base = 'cart-' . ( $current_index + 1 );
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
