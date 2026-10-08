<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<span class="st-widget st-live-visitors st-live-visitors--spotlight" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <strong class="st-live-count"><?php echo (int) $count; ?></strong>
    <span><?php esc_html_e( 'active viewers', 'bk-signals-for-woocommerce' ); ?></span>
</span>
