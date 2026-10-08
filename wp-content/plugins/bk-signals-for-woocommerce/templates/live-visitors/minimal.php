<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
// Live visitors.
?>
<span class="st-widget st-live-visitors st-live-visitors--minimal" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <span class="st-live-dot"></span>
    <span class="st-live-count"><?php echo (int) $count; ?></span>
    <?php esc_html_e( 'people are viewing this product', 'bk-signals-for-woocommerce' ); ?>
</span>
