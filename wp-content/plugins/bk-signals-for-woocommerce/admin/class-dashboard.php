<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Dashboard {

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_global_admin_assets' ] );
        add_action( 'wp_dashboard_setup', [ __CLASS__, 'register_wp_dashboard_widget' ] );
    }

    public static function register_menu() {
        add_menu_page(
            __( 'BK Signals', 'bk-signals-for-woocommerce' ),
            __( 'BK Signals', 'bk-signals-for-woocommerce' ),
            'manage_woocommerce',
            'bk-signals',
            [ __CLASS__, 'render' ],
            BKSIGNALS_URL . 'assets/images/icon.png',
            58
        );

        add_submenu_page( 'bk-signals', __( 'Dashboard', 'bk-signals-for-woocommerce' ),      __( 'Dashboard', 'bk-signals-for-woocommerce' ),      'manage_woocommerce', 'bk-signals',                   [ __CLASS__, 'render' ] );
        add_submenu_page( 'bk-signals', __( 'Live Visitors', 'bk-signals-for-woocommerce' ),   __( 'Live Visitors', 'bk-signals-for-woocommerce' ),  'manage_woocommerce', 'bk-signals-live-visitors',     [ 'BKSignals_Admin_Live_Visitors', 'render' ] );
        add_submenu_page( 'bk-signals', __( 'Views', 'bk-signals-for-woocommerce' ),           __( 'Views', 'bk-signals-for-woocommerce' ),          'manage_woocommerce', 'bk-signals-views',             [ 'BKSignals_Admin_Views', 'render' ] );
        add_submenu_page( 'bk-signals', __( 'Add To Cart', 'bk-signals-for-woocommerce' ),     __( 'Add To Cart', 'bk-signals-for-woocommerce' ),    'manage_woocommerce', 'bk-signals-cart',              [ 'BKSignals_Admin_Cart', 'render' ] );
        add_submenu_page( 'bk-signals', __( 'Sales', 'bk-signals-for-woocommerce' ),           __( 'Sales', 'bk-signals-for-woocommerce' ),          'manage_woocommerce', 'bk-signals-sales',             [ 'BKSignals_Admin_Sales', 'render' ] );
        add_submenu_page( 'bk-signals', __( 'Recent Sales Popup', 'bk-signals-for-woocommerce' ), __( 'Recent Sales Popup', 'bk-signals-for-woocommerce' ), 'manage_woocommerce', 'bk-signals-popup',      [ 'BKSignals_Admin_Popup', 'render' ] );
        add_submenu_page( 'bk-signals', __( 'General Widget', 'bk-signals-for-woocommerce' ),  __( 'General Widget', 'bk-signals-for-woocommerce' ), 'manage_woocommerce', 'bk-signals-general-widget',   [ 'BKSignals_Admin_General_Widget', 'render' ] );
        add_submenu_page( 'bk-signals', __( 'Guide', 'bk-signals-for-woocommerce' ),           __( 'Guide', 'bk-signals-for-woocommerce' ),          'manage_woocommerce', 'bk-signals-guide',            [ __CLASS__, 'render_guide' ] );
        add_submenu_page( 'bk-signals', __( 'Settings', 'bk-signals-for-woocommerce' ),        __( 'Settings', 'bk-signals-for-woocommerce' ),        'manage_woocommerce', 'bk-signals-settings',          [ 'BKSignals_Admin_Settings', 'render' ] );
    }

    public static function enqueue_global_admin_assets( $hook ) {
        wp_register_style( 'bk-signals-global-admin', false, [], BKSIGNALS_VERSION );
        wp_enqueue_style( 'bk-signals-global-admin' );
        $admin_css = wp_strip_all_tags( self::global_admin_css( sanitize_text_field( $hook ) ) );
        wp_add_inline_style( 'bk-signals-global-admin', $admin_css );
    }

    public static function render() {
        $metrics = BKSignals_Database::get_admin_summary_metrics();

        $total_products = $metrics['total_products'];
        $total_views    = $metrics['total_views'];
        $total_sales    = $metrics['total_sales'];
        $total_cart     = $metrics['total_cart'];
        $total_live     = $metrics['total_live'];
        $queued_views   = $metrics['queued_views'];
        $top_products   = BKSignals_Database::get_top_products( 5 );

        $widget_counts = [
            'live'    => self::count_widgets( 'bksignals_live_visitors_widgets' ),
            'views'   => self::count_widgets( 'bksignals_views_widgets' ),
            'cart'    => self::count_widgets( 'bksignals_cart_widgets' ),
            'sales'   => self::count_widgets( 'bksignals_sales_widgets' ),
            'general' => self::count_widgets( 'bksignals_general_widgets' ),
        ];

        $popup_enabled = (bool) get_option( 'bksignals_popup_enabled', true );
        $next_flush = wp_next_scheduled( BKSignals_Cron::HOOK_FLUSH );
        $next_cleanup = wp_next_scheduled( BKSignals_Cron::HOOK_CLEANUP );

        include BKSIGNALS_DIR . 'admin/views/dashboard.php';
    }

    public static function render_guide() {
        include BKSIGNALS_DIR . 'admin/views/guide.php';
    }

    public static function register_wp_dashboard_widget() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        wp_add_dashboard_widget(
            'bksignals_product_activity',
            __( 'BK Signals Product Activity', 'bk-signals-for-woocommerce' ),
            [ __CLASS__, 'render_wp_dashboard_widget' ]
        );
    }

    public static function render_wp_dashboard_widget() {
        $metrics = self::get_summary_metrics();
        ?>
        <div class="st-wp-widget">
            <div class="st-wp-widget-grid">
                <div class="st-wp-widget-card st-wp-widget-card--views">
                    <span class="st-wp-widget-icon" aria-hidden="true"><?php echo wp_kses( self::icon_svg( 'views' ), self::svg_allowed_html() ); ?></span>
                    <div>
                        <div class="st-wp-widget-value"><?php echo esc_html( BKSignals_Helpers::format_number( $metrics['total_views'] ) ); ?></div>
                        <div class="st-wp-widget-label"><?php esc_html_e( 'Total Views', 'bk-signals-for-woocommerce' ); ?></div>
                    </div>
                </div>
                <div class="st-wp-widget-card st-wp-widget-card--cart">
                    <span class="st-wp-widget-icon" aria-hidden="true"><?php echo wp_kses( self::icon_svg( 'cart' ), self::svg_allowed_html() ); ?></span>
                    <div>
                        <div class="st-wp-widget-value"><?php echo esc_html( BKSignals_Helpers::format_number( $metrics['total_cart'] ) ); ?></div>
                        <div class="st-wp-widget-label"><?php esc_html_e( 'Active Carts', 'bk-signals-for-woocommerce' ); ?></div>
                    </div>
                </div>
                <div class="st-wp-widget-card st-wp-widget-card--sales">
                    <span class="st-wp-widget-icon" aria-hidden="true"><?php echo wp_kses( self::icon_svg( 'sales' ), self::svg_allowed_html() ); ?></span>
                    <div>
                        <div class="st-wp-widget-value"><?php echo esc_html( BKSignals_Helpers::format_number( $metrics['total_sales'] ) ); ?></div>
                        <div class="st-wp-widget-label"><?php esc_html_e( 'Total Sales', 'bk-signals-for-woocommerce' ); ?></div>
                    </div>
                </div>
                <div class="st-wp-widget-card st-wp-widget-card--live">
                    <span class="st-wp-widget-icon" aria-hidden="true"><?php echo wp_kses( self::icon_svg( 'live' ), self::svg_allowed_html() ); ?></span>
                    <div>
                        <div class="st-wp-widget-value"><?php echo esc_html( BKSignals_Helpers::format_number( $metrics['total_live'] ) ); ?></div>
                        <div class="st-wp-widget-label"><?php esc_html_e( 'Live Visitors', 'bk-signals-for-woocommerce' ); ?></div>
                    </div>
                </div>
            </div>
            <div class="st-wp-widget-actions">
                <p><?php esc_html_e( 'A quick snapshot of your WooCommerce product activity.', 'bk-signals-for-woocommerce' ); ?></p>
                <a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=bk-signals' ) ); ?>"><?php esc_html_e( 'Open BK Signals', 'bk-signals-for-woocommerce' ); ?></a>
            </div>
        </div>
        <?php
    }

    public static function icon_svg( string $name ): string {
        $paths = [
            'views'   => "M12 5C6.5 5 3 9 2 12c1 3 4.5 7 10 7s9-4 10-7c-1-3-4.5-7-10-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z",
            'cart'    => "M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM3 3h2l2.2 10.2A3 3 0 0 0 10.1 16h6.8a3 3 0 0 0 2.9-2.2L22 6H7.1L6.6 3H3Z",
            'sales'   => "M7 8V7a5 5 0 0 1 10 0v1h2a2 2 0 0 1 2 2l-1.2 10a2 2 0 0 1-2 1.8H6.2a2 2 0 0 1-2-1.8L3 10a2 2 0 0 1 2-2h2Zm2 0h6V7a3 3 0 0 0-6 0v1Zm1.1 7.2-1.4 1.4 2.8 2.8 5.8-5.8-1.4-1.4-4.4 4.4-1.4-1.4Z",
            'live'    => "M12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm-5.7-2.7 1.4 1.4A7 7 0 0 0 7.7 17.7l-1.4 1.4a9 9 0 0 1 0-12.8Zm11.4 0a9 9 0 0 1 0 12.8l-1.4-1.4a7 7 0 0 0 0-9.9l1.4-1.5Z",
            'popup'   => "M5 4h14a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3h-7l-5 4v-4H5a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3Zm3 5v2h8V9H8Zm0 4v2h5v-2H8Z",
            'general' => "M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z",
        ];

        $path = $paths[ $name ] ?? $paths['general'];

        return '<svg class="st-dashboard-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="' . esc_attr( $path ) . '"></path></svg>';
    }

    public static function svg_allowed_html(): array {
        return [
            'svg'  => [
                'class'       => true,
                'xmlns'       => true,
                'width'       => true,
                'height'      => true,
                'viewbox'     => true,
                'fill'        => true,
                'stroke'      => true,
                'stroke-width' => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
                'aria-hidden' => true,
                'focusable'   => true,
            ],
            'path' => [
                'd' => true,
            ],
            'circle' => [
                'cx' => true,
                'cy' => true,
                'r'  => true,
            ],
            'polyline' => [
                'points' => true,
            ],
        ];
    }

    private static function get_summary_metrics(): array {
        $metrics = BKSignals_Database::get_admin_summary_metrics();

        return [
            'total_views' => (int) $metrics['total_views'],
            'total_cart'  => (int) $metrics['total_cart'],
            'total_sales' => (int) $metrics['total_sales'],
            'total_live'  => (int) $metrics['total_live'],
        ];
    }

    public static function format_admin_datetime( $timestamp ): string {
        if ( ! $timestamp ) {
            return __( 'Not scheduled', 'bk-signals-for-woocommerce' );
        }

        $date = new DateTimeImmutable( '@' . (int) $timestamp );
        $date = $date->setTimezone( wp_timezone() );

        return $date->format( 'M j, H:i' );
    }

    private static function count_widgets( string $option_name ): int {
        $widgets = get_option( $option_name, [] );
        if ( ! is_array( $widgets ) ) {
            return 0;
        }

        return count( array_filter( $widgets, static function ( $widget ) {
            return ! is_array( $widget ) || ! array_key_exists( 'enabled', $widget ) || ! empty( $widget['enabled'] );
        } ) );
    }

    private static function global_admin_css( $hook ): string {
        $css = '#adminmenu .toplevel_page_bk-signals .wp-menu-image img{width:32px;height:20px;max-width:none;object-fit:contain;padding-top:8px;opacity:1;}';

        if ( 'index.php' !== $hook ) {
            return $css;
        }

        return $css . '.st-wp-widget{display:grid;gap:14px}.st-wp-widget-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.st-wp-widget-card{display:grid;grid-template-columns:34px minmax(0,1fr);gap:10px;align-items:center;min-height:62px;padding:12px;border:1px solid #e2e8f0;border-radius:12px;background:#fff}.st-wp-widget-card--views{background:linear-gradient(135deg,#fff,#eff6ff)}.st-wp-widget-card--cart{background:linear-gradient(135deg,#fff,#fff7ed)}.st-wp-widget-card--sales{background:linear-gradient(135deg,#fff,#f0fdf4)}.st-wp-widget-card--live{background:linear-gradient(135deg,#fff,#fffbeb)}.st-wp-widget-icon{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;background:#eff6ff;color:#2563eb}.st-wp-widget-card--cart .st-wp-widget-icon{background:#fff7ed;color:#f97316}.st-wp-widget-card--sales .st-wp-widget-icon{background:#f0fdf4;color:#047857}.st-wp-widget-card--live .st-wp-widget-icon{background:#fef3c7;color:#b45309}.st-wp-widget-icon .st-dashboard-svg{display:block;width:18px;height:18px;fill:currentColor}.st-wp-widget-value{color:#0f172a;font-size:22px;font-weight:900;line-height:1}.st-wp-widget-label{margin-top:4px;color:#64748b;font-size:12px;font-weight:700}.st-wp-widget-actions{display:flex;align-items:center;justify-content:space-between;gap:10px;padding-top:2px}.st-wp-widget-actions p{margin:0;color:#64748b;font-size:12px}@media (max-width:520px){.st-wp-widget-grid{grid-template-columns:1fr}.st-wp-widget-actions{align-items:flex-start;flex-direction:column}}';
    }
}
