<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<span class="st-widget st-live-visitors st-live-visitors--pulse" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <span class="st-live-dot"></span>
    <strong class="st-live-count"><?php echo (int) $count; ?></strong>
    <span><?php esc_html_e( 'live visitors', 'bk-signals-for-woocommerce' ); ?></span>
</span>
