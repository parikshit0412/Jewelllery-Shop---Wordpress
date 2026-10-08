<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<div class="st-widget st-card-widget st-live-visitors st-live-visitors--card" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <div class="st-card-icon"><span class="st-live-dot"></span></div>
    <div class="st-card-body">
        <div class="st-card-value st-live-count"><?php echo (int) $count; ?></div>
        <div class="st-card-label"><?php esc_html_e( 'Viewing Now', 'bk-signals-for-woocommerce' ); ?></div>
    </div>
</div>
