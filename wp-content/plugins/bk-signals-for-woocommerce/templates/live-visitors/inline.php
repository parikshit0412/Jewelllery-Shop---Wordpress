<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<span class="st-widget st-live-visitors st-live-visitors--inline" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <?php esc_html_e( 'Currently', 'bk-signals-for-woocommerce' ); ?>
    <strong class="st-live-count"><?php echo (int) $count; ?></strong>
    <?php esc_html_e( 'people viewing', 'bk-signals-for-woocommerce' ); ?>
</span>
