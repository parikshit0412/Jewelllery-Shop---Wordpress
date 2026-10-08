<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<?php
$guide_sections = [
    [
        'icon' => 'live',
        'title' => __( 'Live Visitors', 'bk-signals-for-woocommerce' ),
        'summary' => __( 'Shows how many visitors are viewing the current product right now.', 'bk-signals-for-woocommerce' ),
        'details' => __( 'Use it near the product title, price, or add-to-cart area to create a real-time urgency signal without heavy live queries.', 'bk-signals-for-woocommerce' ),
    ],
    [
        'icon' => 'views',
        'title' => __( 'Views', 'bk-signals-for-woocommerce' ),
        'summary' => __( 'Displays product view counts for a selected time window.', 'bk-signals-for-woocommerce' ),
        'details' => __( 'Useful for showing popularity such as views in the last 24 hours, 7 days, 30 days, 90 days, or all time.', 'bk-signals-for-woocommerce' ),
    ],
    [
        'icon' => 'cart',
        'title' => __( 'Active Carts', 'bk-signals-for-woocommerce' ),
        'summary' => __( 'Shows how many visitors currently have the product in their cart.', 'bk-signals-for-woocommerce' ),
        'details' => __( 'This is designed as a current demand signal, so removing a product from the cart lowers the active cart count.', 'bk-signals-for-woocommerce' ),
    ],
    [
        'icon' => 'sales',
        'title' => __( 'Sales', 'bk-signals-for-woocommerce' ),
        'summary' => __( 'Shows real WooCommerce purchase activity.', 'bk-signals-for-woocommerce' ),
        'details' => __( 'Sales are based on accepted WooCommerce order statuses and admin-created orders are excluded from the storefront counters.', 'bk-signals-for-woocommerce' ),
    ],
    [
        'icon' => 'popup',
        'title' => __( 'Recent Sales Popup', 'bk-signals-for-woocommerce' ),
        'summary' => __( 'Displays compact recent purchase notifications on the storefront.', 'bk-signals-for-woocommerce' ),
        'details' => __( 'Use it to rotate real recent orders with masked customer names, shortened product titles, product images, and mobile-aware positioning.', 'bk-signals-for-woocommerce' ),
    ],
    [
        'icon' => 'general',
        'title' => __( 'General Widget', 'bk-signals-for-woocommerce' ),
        'summary' => __( 'Combines views, active carts, sales, and live visitors in one block.', 'bk-signals-for-woocommerce' ),
        'details' => __( 'Best for stores that want one clean product activity area instead of several separate shortcodes.', 'bk-signals-for-woocommerce' ),
    ],
];
?>
<div class="wrap st-admin st-guide-page">
    <div class="st-dashboard-hero st-guide-hero">
        <div>
            <p class="st-eyebrow"><?php esc_html_e( 'BK Signals Guide', 'bk-signals-for-woocommerce' ); ?></p>
            <h1><?php esc_html_e( 'Feature Documentation', 'bk-signals-for-woocommerce' ); ?></h1>
            <p><?php esc_html_e( 'Understand what each BK Signals module does, where to place it, and how it helps WooCommerce product pages feel more active and trustworthy.', 'bk-signals-for-woocommerce' ); ?></p>
        </div>
        <a class="button button-primary st-button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=bk-signals-settings' ) ); ?>"><?php esc_html_e( 'Open Settings', 'bk-signals-for-woocommerce' ); ?></a>
    </div>

    <div class="st-guide-layout">
        <main class="st-guide-main">
            <section class="st-card st-dashboard-section">
                <div class="st-section-head">
                    <div>
                        <h2><?php esc_html_e( 'Feature Overview', 'bk-signals-for-woocommerce' ); ?></h2>
                        <p><?php esc_html_e( 'Each module can be configured, styled, and inserted with its own shortcode.', 'bk-signals-for-woocommerce' ); ?></p>
                    </div>
                </div>
                <div class="st-guide-grid">
                    <?php foreach ( $guide_sections as $section ) : ?>
                        <article class="st-guide-card">
                            <span class="st-guide-icon st-dashboard-icon st-dashboard-icon--<?php echo esc_attr( $section['icon'] ); ?>" aria-hidden="true"><?php echo wp_kses( BKSignals_Dashboard::icon_svg( $section['icon'] ), BKSignals_Dashboard::svg_allowed_html() ); ?></span>
                            <div>
                                <h3><?php echo esc_html( $section['title'] ); ?></h3>
                                <p><strong><?php echo esc_html( $section['summary'] ); ?></strong></p>
                                <p><?php echo esc_html( $section['details'] ); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="st-card st-dashboard-section st-guide-workflow">
                <div class="st-section-head">
                    <div>
                        <h2><?php esc_html_e( 'Recommended Workflow', 'bk-signals-for-woocommerce' ); ?></h2>
                        <p><?php esc_html_e( 'A simple setup flow for a store owner using BK Signals for the first time.', 'bk-signals-for-woocommerce' ); ?></p>
                    </div>
                </div>
                <ol>
                    <li><?php esc_html_e( 'Create a widget for the module you want to show.', 'bk-signals-for-woocommerce' ); ?></li>
                    <li><?php esc_html_e( 'Choose a design and preview it inside the admin page.', 'bk-signals-for-woocommerce' ); ?></li>
                    <li><?php esc_html_e( 'Adjust the time window, mobile behavior, and refresh settings where available.', 'bk-signals-for-woocommerce' ); ?></li>
                    <li><?php esc_html_e( 'Copy the shortcode and place it on product pages, theme templates, or page builder sections.', 'bk-signals-for-woocommerce' ); ?></li>
                </ol>
            </section>
        </main>

    </div>
</div>
