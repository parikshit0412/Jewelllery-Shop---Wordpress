<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
?>
<div class="st-widget st-cart st-cart--metric-card" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <div class="st-cart-metric-header">
        <svg class="st-cart-icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
        <span><?php esc_html_e( 'Active Carts', 'bk-signals-for-woocommerce' ); ?></span>
    </div>
    <strong><span class="st-stat-count" data-stat="active_cart"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span></strong>
    <span><?php esc_html_e( 'in carts now', 'bk-signals-for-woocommerce' ); ?></span>
</div>
