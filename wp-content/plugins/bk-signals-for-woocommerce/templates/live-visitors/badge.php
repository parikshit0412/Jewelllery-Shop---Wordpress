<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<span class="st-widget st-badge st-live-visitors st-live-visitors--badge" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <span class="st-live-dot"></span>
    <span class="st-live-count"><?php echo (int) $count; ?></span>
    <?php esc_html_e( 'active', 'bk-signals-for-woocommerce' ); ?>
</span>
