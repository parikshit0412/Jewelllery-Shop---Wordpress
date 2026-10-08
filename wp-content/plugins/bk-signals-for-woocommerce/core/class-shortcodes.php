<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Shortcodes {

    public static function init() {
        add_shortcode( 'bk_signals_live_visitors', [ __CLASS__, 'live_visitors' ] );
        add_shortcode( 'bk_signals_views',         [ __CLASS__, 'views' ] );
        add_shortcode( 'bk_signals_add_to_cart',   [ __CLASS__, 'add_to_cart' ] );
        add_shortcode( 'bk_signals_sales',         [ __CLASS__, 'sales' ] );
        add_shortcode( 'bk_signals_general',       [ __CLASS__, 'general' ] );
    }

    private static function parse( $atts, $defaults = [] ) {
        return shortcode_atts( array_merge( [
            'id'     => get_the_ID(),
            'design' => 'minimal',
            'widget' => '',
            'window' => '7',
        ], $defaults ), $atts );
    }

    public static function live_visitors( $atts ) {
        $a          = self::parse( $atts );
        $product_id = absint( $a['id'] );
        if ( ! $product_id ) return '';

        if ( ! empty( $a['widget'] ) ) {
            $widget = self::live_visitor_widget_by_id( sanitize_text_field( $a['widget'] ) );
            if ( empty( $widget ) || ( array_key_exists( 'enabled', $widget ) && empty( $widget['enabled'] ) ) ) {
                return '';
            }
            $a['design'] = $widget['design'] ?? $a['design'];
        }

        $count = BKSignals_Database::count_live_visitors( $product_id );
        if ( $count < 1 ) $count = 0;

        ob_start();
        include self::template( 'live-visitors', $a['design'] );
        return ob_get_clean();
    }

    public static function views( $atts ) {
        $a          = self::parse( $atts );
        $product_id = absint( $a['id'] );
        if ( ! $product_id ) return '';

        if ( ! empty( $a['widget'] ) ) {
            $widget = self::view_widget_by_id( sanitize_text_field( $a['widget'] ) );
            if ( empty( $widget ) || ( array_key_exists( 'enabled', $widget ) && empty( $widget['enabled'] ) ) ) {
                return '';
            }
            $a['design'] = $widget['design'] ?? $a['design'];
            $a['window'] = $widget['window'] ?? $a['window'];
        }

        $window = (int) $a['window'];
        $count  = BKSignals_Database::get_product_metric_for_window( $product_id, 'views', $window );
        $window_label = self::window_label( $window );

        ob_start();
        include self::template( 'views', $a['design'] );
        return ob_get_clean();
    }

    public static function add_to_cart( $atts ) {
        $a          = self::parse( $atts );
        $product_id = absint( $a['id'] );
        if ( ! $product_id ) return '';

        if ( ! empty( $a['widget'] ) ) {
            $widget = self::cart_widget_by_id( sanitize_text_field( $a['widget'] ) );
            if ( empty( $widget ) || ( array_key_exists( 'enabled', $widget ) && empty( $widget['enabled'] ) ) ) {
                return '';
            }
            $a['design'] = $widget['design'] ?? $a['design'];
        }

        $count = BKSignals_Database::count_active_carts( $product_id );

        ob_start();
        include self::template( 'add-to-cart', $a['design'] );
        return ob_get_clean();
    }

    public static function sales( $atts ) {
        $a          = self::parse( $atts );
        $product_id = absint( $a['id'] );
        if ( ! $product_id ) return '';

        if ( ! empty( $a['widget'] ) ) {
            $widget = self::sales_widget_by_id( sanitize_text_field( $a['widget'] ) );
            if ( empty( $widget ) || ( array_key_exists( 'enabled', $widget ) && empty( $widget['enabled'] ) ) ) {
                return '';
            }
            $a['design'] = $widget['design'] ?? $a['design'];
            $a['window'] = $widget['window'] ?? $a['window'];
        }

        $stats  = BKSignals_Database::get_product_stats( $product_id );
        $window = (int) $a['window'];
        $count  = BKSignals_Database::get_product_metric_for_window( $product_id, 'sales', $window );
        $window_label = self::window_label( $window );

        ob_start();
        include self::template( 'sales', $a['design'] );
        return ob_get_clean();
    }

    public static function general( $atts ) {
        $a          = self::parse( $atts, [ 'design' => 'trend-alert' ] );
        $product_id = absint( $a['id'] );
        if ( ! $product_id ) return '';

        if ( ! empty( $a['widget'] ) ) {
            $widget = self::general_widget_by_id( sanitize_text_field( $a['widget'] ) );
            if ( empty( $widget ) || ( array_key_exists( 'enabled', $widget ) && empty( $widget['enabled'] ) ) ) {
                return '';
            }
            $a['design']     = $widget['design'] ?? $a['design'];
            $a['window']     = $widget['window'] ?? $a['window'];
            $a['show_views'] = $widget['show_views'] ?? true;
            $a['show_cart']  = $widget['show_cart'] ?? true;
            $a['show_sales'] = $widget['show_sales'] ?? true;
            $a['show_live']  = $widget['show_live'] ?? true;
        }

        $stats      = BKSignals_Database::get_product_stats( $product_id );
        $window     = (int) $a['window'];
        $live_count = BKSignals_Database::count_live_visitors( $product_id );
        $active_cart_count = BKSignals_Database::count_active_carts( $product_id );
        $window_label = self::window_label( $window );
        $window_title_label = self::window_title_label( $window );

        $show_views = self::truthy( $a['show_views'] ?? true );
        $show_cart  = self::truthy( $a['show_cart'] ?? true );
        $show_sales = self::truthy( $a['show_sales'] ?? true );
        $show_live  = self::truthy( $a['show_live'] ?? true );

        $general_metrics = [
            'views' => [
                'show'  => $show_views,
                'value' => BKSignals_Database::get_product_metric_for_window( $product_id, 'views', $window ),
                'label' => __( 'Views', 'bk-signals-for-woocommerce' ),
            ],
            'cart' => [
                'show'  => $show_cart,
                'value' => (int) $active_cart_count,
                'label' => __( 'In carts', 'bk-signals-for-woocommerce' ),
            ],
            'sales' => [
                'show'  => $show_sales,
                'value' => BKSignals_Database::get_product_metric_for_window( $product_id, 'sales', $window ),
                'label' => __( 'Sales', 'bk-signals-for-woocommerce' ),
            ],
            'live' => [
                'show'  => $show_live,
                'value' => (int) $live_count,
                'label' => __( 'Viewing now', 'bk-signals-for-woocommerce' ),
            ],
        ];

        ob_start();
        include self::template( 'general', $a['design'] );
        return ob_get_clean();
    }

    private static function stat_for_window( array $stats, string $prefix, int $window ): int {
        if ( $window <= 1 )  return (int) ( $stats[ $prefix . '_24h' ] ?? 0 );
        if ( $window <= 7 )  return (int) ( $stats[ $prefix . '_7d' ] ?? 0 );
        if ( $window <= 30 ) return (int) ( $stats[ $prefix . '_30d' ] ?? 0 );
        return (int) ( $stats[ $prefix . '_total' ] ?? 0 );
    }

    private static function window_label( int $window ): string {
        if ( $window >= 999 ) {
            return __( 'all time', 'bk-signals-for-woocommerce' );
        }
        if ( $window <= 1 ) {
            return __( 'the last 24 hours', 'bk-signals-for-woocommerce' );
        }
        /* translators: %d: selected time window in days. */
        return sprintf( __( 'the last %d days', 'bk-signals-for-woocommerce' ), $window );
    }

    private static function window_title_label( int $window ): string {
        if ( $window >= 999 ) {
            return __( 'All Time', 'bk-signals-for-woocommerce' );
        }
        if ( $window <= 1 ) {
            return __( 'The Last 24 Hours', 'bk-signals-for-woocommerce' );
        }
        /* translators: %d: selected time window in days. */
        return sprintf( __( 'The Last %d Days', 'bk-signals-for-woocommerce' ), $window );
    }

    private static function truthy( $value ): bool {
        if ( is_bool( $value ) ) {
            return $value;
        }
        return in_array( strtolower( (string) $value ), [ '1', 'true', 'yes', 'on' ], true );
    }

    private static function live_visitor_widget_by_id( string $widget_id ): array {
        $widget_id = sanitize_text_field( $widget_id );
        $widgets = get_option( 'bksignals_live_visitors_widgets', [] );
        foreach ( $widgets as $widget ) {
            if ( ! empty( $widget['shortcode_key'] ) && $widget_id === $widget['shortcode_key'] ) {
                return $widget;
            }
            if ( ! empty( $widget['id'] ) && $widget_id === $widget['id'] ) {
                return $widget;
            }
        }
        return [];
    }

    private static function view_widget_by_id( string $widget_id ): array {
        $widget_id = sanitize_text_field( $widget_id );
        $widgets = get_option( 'bksignals_views_widgets', [] );
        foreach ( $widgets as $widget ) {
            if ( ! empty( $widget['shortcode_key'] ) && $widget_id === $widget['shortcode_key'] ) {
                return $widget;
            }
            if ( ! empty( $widget['id'] ) && $widget_id === $widget['id'] ) {
                return $widget;
            }
        }
        return [];
    }

    private static function cart_widget_by_id( string $widget_id ): array {
        $widget_id = sanitize_text_field( $widget_id );
        $widgets = get_option( 'bksignals_cart_widgets', [] );
        foreach ( $widgets as $widget ) {
            if ( ! empty( $widget['shortcode_key'] ) && $widget_id === $widget['shortcode_key'] ) {
                return $widget;
            }
            if ( ! empty( $widget['id'] ) && $widget_id === $widget['id'] ) {
                return $widget;
            }
        }
        return [];
    }

    private static function sales_widget_by_id( string $widget_id ): array {
        $widget_id = sanitize_text_field( $widget_id );
        $widgets = get_option( 'bksignals_sales_widgets', [] );
        foreach ( $widgets as $widget ) {
            if ( ! empty( $widget['shortcode_key'] ) && $widget_id === $widget['shortcode_key'] ) {
                return $widget;
            }
            if ( ! empty( $widget['id'] ) && $widget_id === $widget['id'] ) {
                return $widget;
            }
        }
        return [];
    }

    private static function general_widget_by_id( string $widget_id ): array {
        $widget_id = sanitize_text_field( $widget_id );
        $widgets = get_option( 'bksignals_general_widgets', [] );
        foreach ( $widgets as $widget ) {
            if ( ! empty( $widget['shortcode_key'] ) && $widget_id === $widget['shortcode_key'] ) {
                return $widget;
            }
            if ( ! empty( $widget['id'] ) && $widget_id === $widget['id'] ) {
                return $widget;
            }
        }
        return [];
    }

    private static function template( string $module, string $design ): string {
        $file = BKSIGNALS_DIR . "templates/{$module}/{$design}.php";
        if ( file_exists( $file ) ) return $file;

        $fallback = BKSIGNALS_DIR . "templates/{$module}/minimal.php";
        if ( file_exists( $fallback ) ) return $fallback;

        return BKSIGNALS_DIR . 'templates/fallback.php';
    }
}
