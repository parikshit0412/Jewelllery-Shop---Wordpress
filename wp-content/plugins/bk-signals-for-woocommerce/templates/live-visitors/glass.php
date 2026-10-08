<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<span class="st-widget st-live-visitors st-live-visitors--glass" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <strong class="st-live-count"><?php echo (int) $count; ?></strong>
    <span><?php esc_html_e( 'people are here now', 'bk-signals-for-woocommerce' ); ?></span>
</span>
