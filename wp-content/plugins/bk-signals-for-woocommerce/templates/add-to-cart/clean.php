<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
?>
<div class="st-widget st-cart st-cart--clean" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <strong><span class="st-stat-count" data-stat="active_cart"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span></strong>
    <span><?php esc_html_e( 'people have this in cart', 'bk-signals-for-woocommerce' ); ?></span>
</div>
