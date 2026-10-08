<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<div class="st-widget st-live-visitors st-live-visitors--modern" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <span class="st-live-icon"><span class="st-live-dot"></span></span>
    <div class="st-content">
        <strong class="st-live-count"><?php echo (int) $count; ?></strong>
        <span><?php esc_html_e( 'people are viewing this product now', 'bk-signals-for-woocommerce' ); ?></span>
    </div>
</div>
