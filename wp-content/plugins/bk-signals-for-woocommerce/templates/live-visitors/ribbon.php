<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<span class="st-widget st-live-visitors st-live-visitors--ribbon" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <span class="st-ribbon-label"><?php esc_html_e( 'LIVE', 'bk-signals-for-woocommerce' ); ?></span>
    <strong class="st-live-count"><?php echo (int) $count; ?></strong>
</span>
