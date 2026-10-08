<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<?php
$modules = [
    [
        'key' => 'live',
        'title' => __( 'Live Visitors', 'bk-signals-for-woocommerce' ),
        'icon' => 'live',
        'text' => __( 'Show how many people are viewing a product right now.', 'bk-signals-for-woocommerce' ),
        'url' => admin_url( 'admin.php?page=bk-signals-live-visitors' ),
        'count' => $widget_counts['live'] ?? 0,
    ],
    [
        'key' => 'views',
        'title' => __( 'Views', 'bk-signals-for-woocommerce' ),
        'icon' => 'views',
        'text' => __( 'Display product view counts by time window.', 'bk-signals-for-woocommerce' ),
        'url' => admin_url( 'admin.php?page=bk-signals-views' ),
        'count' => $widget_counts['views'] ?? 0,
    ],
    [
        'key' => 'cart',
        'title' => __( 'Add To Cart', 'bk-signals-for-woocommerce' ),
        'icon' => 'cart',
        'text' => __( 'Show current in-cart demand for each product.', 'bk-signals-for-woocommerce' ),
        'url' => admin_url( 'admin.php?page=bk-signals-cart' ),
        'count' => $widget_counts['cart'] ?? 0,
    ],
    [
        'key' => 'sales',
        'title' => __( 'Sales', 'bk-signals-for-woocommerce' ),
        'icon' => 'sales',
        'text' => __( 'Show verified WooCommerce purchase activity.', 'bk-signals-for-woocommerce' ),
        'url' => admin_url( 'admin.php?page=bk-signals-sales' ),
        'count' => $widget_counts['sales'] ?? 0,
    ],
    [
        'key' => 'popup',
        'title' => __( 'Recent Sales Popup', 'bk-signals-for-woocommerce' ),
        'icon' => 'popup',
        'text' => __( 'Rotate real recent sales notifications on the storefront.', 'bk-signals-for-woocommerce' ),
        'url' => admin_url( 'admin.php?page=bk-signals-popup' ),
        'count' => $popup_enabled ? 1 : 0,
    ],
    [
        'key' => 'general',
        'title' => __( 'General Widget', 'bk-signals-for-woocommerce' ),
        'icon' => 'general',
        'text' => __( 'Combine views, carts, sales, and live activity in one block.', 'bk-signals-for-woocommerce' ),
        'url' => admin_url( 'admin.php?page=bk-signals-general-widget' ),
        'count' => $widget_counts['general'] ?? 0,
    ],
];

$feature_docs = [
    [
        'icon' => 'live',
        'title' => __( 'Live Visitors', 'bk-signals-for-woocommerce' ),
        'text' => __( 'Displays how many visitors are currently viewing the same product. Best for urgency near the product title or buy area.', 'bk-signals-for-woocommerce' ),
    ],
    [
        'icon' => 'views',
        'title' => __( 'Views', 'bk-signals-for-woocommerce' ),
        'text' => __( 'Shows product view counts by selected time window, such as the last 24 hours, 7 days, 30 days, or all time.', 'bk-signals-for-woocommerce' ),
    ],
    [
        'icon' => 'cart',
        'title' => __( 'Active Carts', 'bk-signals-for-woocommerce' ),
        'text' => __( 'Shows how many visitors currently have the product in their cart, giving a more accurate demand signal.', 'bk-signals-for-woocommerce' ),
    ],
    [
        'icon' => 'sales',
        'title' => __( 'Sales', 'bk-signals-for-woocommerce' ),
        'text' => __( 'Shows real WooCommerce purchase activity based on accepted order statuses, while excluding admin-created orders.', 'bk-signals-for-woocommerce' ),
    ],
];
?>
<div class="wrap st-admin st-dashboard-page">
    <div class="st-dashboard-hero">
        <div>
            <p class="st-eyebrow"><?php esc_html_e( 'WooCommerce Social Proof', 'bk-signals-for-woocommerce' ); ?></p>
            <h1><?php esc_html_e( 'BK Signals Dashboard', 'bk-signals-for-woocommerce' ); ?></h1>
            <p><?php esc_html_e( 'Monitor product activity, manage widgets, and keep your social proof data ready for deeper insights.', 'bk-signals-for-woocommerce' ); ?></p>
        </div>
        <div class="st-dashboard-hero-actions">
            <a class="button st-secondary-button" href="<?php echo esc_url( admin_url( 'admin.php?page=bk-signals-guide' ) ); ?>"><?php esc_html_e( 'View Guide', 'bk-signals-for-woocommerce' ); ?></a>
            <a class="button button-primary st-button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=bk-signals-settings' ) ); ?>"><?php esc_html_e( 'Open Settings', 'bk-signals-for-woocommerce' ); ?></a>
        </div>
    </div>

    <?php if ( BKSignals_Helpers::is_admin_notice( 'saved' ) ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'bk-signals-for-woocommerce' ); ?></p></div>
    <?php endif; ?>

    <div class="st-dashboard-layout">
        <main class="st-dashboard-main">
            <div class="st-stats-grid">
                <div class="st-stat-card st-stat-card--views">
                    <span class="st-stat-icon st-dashboard-icon st-dashboard-icon--views" aria-hidden="true"><?php echo wp_kses( BKSignals_Dashboard::icon_svg( 'views' ), BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                    <div class="st-stat-content">
                        <span class="st-stat-period"><?php esc_html_e( 'All Time', 'bk-signals-for-woocommerce' ); ?></span>
                        <div class="st-stat-value"><?php echo esc_html( BKSignals_Helpers::format_number( $total_views ) ); ?></div>
                        <div class="st-stat-label"><?php esc_html_e( 'Total Views', 'bk-signals-for-woocommerce' ); ?></div>
                    </div>
                </div>
                <div class="st-stat-card st-stat-card--cart">
                    <span class="st-stat-icon st-dashboard-icon st-dashboard-icon--cart" aria-hidden="true"><?php echo wp_kses( BKSignals_Dashboard::icon_svg( 'cart' ), BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                    <div class="st-stat-content">
                        <span class="st-stat-period"><?php esc_html_e( 'Current', 'bk-signals-for-woocommerce' ); ?></span>
                        <div class="st-stat-value"><?php echo esc_html( BKSignals_Helpers::format_number( (int) $total_cart ) ); ?></div>
                        <div class="st-stat-label"><?php esc_html_e( 'Active Carts', 'bk-signals-for-woocommerce' ); ?></div>
                    </div>
                </div>
                <div class="st-stat-card st-stat-card--sales">
                    <span class="st-stat-icon st-dashboard-icon st-dashboard-icon--sales" aria-hidden="true"><?php echo wp_kses( BKSignals_Dashboard::icon_svg( 'sales' ), BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                    <div class="st-stat-content">
                        <span class="st-stat-period"><?php esc_html_e( 'All Time', 'bk-signals-for-woocommerce' ); ?></span>
                        <div class="st-stat-value"><?php echo esc_html( BKSignals_Helpers::format_number( $total_sales ) ); ?></div>
                        <div class="st-stat-label"><?php esc_html_e( 'Total Sales', 'bk-signals-for-woocommerce' ); ?></div>
                    </div>
                </div>
                <div class="st-stat-card st-stat-card--live">
                    <span class="st-stat-icon st-dashboard-icon st-dashboard-icon--live" aria-hidden="true"><?php echo wp_kses( BKSignals_Dashboard::icon_svg( 'live' ), BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                    <div class="st-stat-content">
                        <span class="st-stat-period"><?php esc_html_e( 'Now', 'bk-signals-for-woocommerce' ); ?></span>
                        <div class="st-stat-value"><?php echo esc_html( BKSignals_Helpers::format_number( $total_live ) ); ?></div>
                        <div class="st-stat-label"><?php esc_html_e( 'Live Visitors', 'bk-signals-for-woocommerce' ); ?></div>
                    </div>
                </div>
            </div>

            <section class="st-card st-dashboard-section">
                <div class="st-section-head">
                    <div>
                        <h2><?php esc_html_e( 'Quick Access', 'bk-signals-for-woocommerce' ); ?></h2>
                        <p><?php esc_html_e( 'Create, edit, and copy shortcodes for each social proof module.', 'bk-signals-for-woocommerce' ); ?></p>
                    </div>
                </div>
                <div class="st-module-grid">
                    <?php foreach ( $modules as $module ) : ?>
                        <a href="<?php echo esc_url( $module['url'] ); ?>" class="st-module-card st-module-card--<?php echo esc_attr( $module['key'] ); ?>">
                            <span class="st-module-icon st-dashboard-icon st-dashboard-icon--<?php echo esc_attr( $module['icon'] ); ?>" aria-hidden="true"><?php echo wp_kses( BKSignals_Dashboard::icon_svg( $module['icon'] ), BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                            <span class="st-module-copy">
                                <strong><?php echo esc_html( $module['title'] ); ?></strong>
                                <small><?php echo esc_html( $module['text'] ); ?></small>
                            </span>
                            <span class="st-status-badge <?php echo esc_attr( $module['count'] > 0 ? 'is-active' : 'is-muted' ); ?>">
                                <?php
                                if ( $module['count'] > 0 ) {
                                    /* translators: %d: number of active widgets. */
                                    echo esc_html( sprintf( _n( '%d active', '%d active', (int) $module['count'], 'bk-signals-for-woocommerce' ), (int) $module['count'] ) );
                                } else {
                                    esc_html_e( 'Not active', 'bk-signals-for-woocommerce' );
                                }
                                ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="st-card st-dashboard-section st-feature-guide">
                <div class="st-section-head">
                    <div>
                        <h2><?php esc_html_e( 'Feature Guide', 'bk-signals-for-woocommerce' ); ?></h2>
                        <p><?php esc_html_e( 'A quick reference for what each BK Signals feature does and where it fits on a product page.', 'bk-signals-for-woocommerce' ); ?></p>
                    </div>
                    <a class="button st-secondary-button" href="<?php echo esc_url( admin_url( 'admin.php?page=bk-signals-guide' ) ); ?>"><?php esc_html_e( 'Open Full Guide', 'bk-signals-for-woocommerce' ); ?></a>
                </div>
                <div class="st-guide-mini-grid">
                    <?php foreach ( $feature_docs as $doc ) : ?>
                        <article class="st-guide-mini-card">
                            <span class="st-guide-mini-icon st-dashboard-icon st-dashboard-icon--<?php echo esc_attr( $doc['icon'] ); ?>" aria-hidden="true"><?php echo wp_kses( BKSignals_Dashboard::icon_svg( $doc['icon'] ), BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                            <div>
                                <strong><?php echo esc_html( $doc['title'] ); ?></strong>
                                <p><?php echo esc_html( $doc['text'] ); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="st-card st-dashboard-section">
                <div class="st-section-head">
                    <div>
                        <h2><?php esc_html_e( 'Top 5 Product Activity', 'bk-signals-for-woocommerce' ); ?></h2>
                        <p><?php esc_html_e( 'A quick snapshot of the five products with the strongest tracked activity.', 'bk-signals-for-woocommerce' ); ?></p>
                    </div>
                </div>
                <?php if ( ! empty( $top_products ) ) : ?>
                    <table class="widefat st-dashboard-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Product', 'bk-signals-for-woocommerce' ); ?></th>
                                <th><?php esc_html_e( 'Views', 'bk-signals-for-woocommerce' ); ?></th>
                                <th><?php esc_html_e( 'Carts', 'bk-signals-for-woocommerce' ); ?></th>
                                <th><?php esc_html_e( 'Sales', 'bk-signals-for-woocommerce' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $top_products as $product ) : ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?php
                                            if ( ! empty( $product['post_title'] ) ) {
                                                echo esc_html( $product['post_title'] );
                                            } else {
                                                /* translators: %d: product ID. */
                                                echo esc_html( sprintf( __( 'Product #%d', 'bk-signals-for-woocommerce' ), (int) $product['product_id'] ) );
                                            }
                                            ?>
                                        </strong>
                                    </td>
                                    <td><?php echo esc_html( BKSignals_Helpers::format_number( (int) $product['views_total'] ) ); ?></td>
                                    <td><?php echo esc_html( BKSignals_Helpers::format_number( (int) $product['cart_total'] ) ); ?></td>
                                    <td><?php echo esc_html( BKSignals_Helpers::format_number( (int) $product['sales_total'] ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="st-empty-state">
                        <strong><?php esc_html_e( 'No product activity yet.', 'bk-signals-for-woocommerce' ); ?></strong>
                        <span><?php esc_html_e( 'As visitors view products and interact with your store, this area will fill automatically.', 'bk-signals-for-woocommerce' ); ?></span>
                    </div>
                <?php endif; ?>
            </section>
        </main>

        <aside class="st-dashboard-sidebar">
            <section class="st-card st-system-card">
                <h2><?php esc_html_e( 'System Status', 'bk-signals-for-woocommerce' ); ?></h2>
                <div class="st-status-row">
                    <span><?php esc_html_e( 'Queued views', 'bk-signals-for-woocommerce' ); ?></span>
                    <strong><?php echo esc_html( BKSignals_Helpers::format_number( $queued_views ) ); ?></strong>
                </div>
                <div class="st-status-row">
                    <span><?php esc_html_e( 'Tracked products', 'bk-signals-for-woocommerce' ); ?></span>
                    <strong><?php echo esc_html( BKSignals_Helpers::format_number( $total_products ) ); ?></strong>
                </div>
                <div class="st-status-row">
                    <span><?php esc_html_e( 'Next data flush', 'bk-signals-for-woocommerce' ); ?></span>
                    <strong><?php echo esc_html( BKSignals_Dashboard::format_admin_datetime( $next_flush ) ); ?></strong>
                </div>
                <div class="st-status-row">
                    <span><?php esc_html_e( 'Next cleanup', 'bk-signals-for-woocommerce' ); ?></span>
                    <strong><?php echo esc_html( BKSignals_Dashboard::format_admin_datetime( $next_cleanup ) ); ?></strong>
                </div>
            </section>
        </aside>
    </div>
</div>
