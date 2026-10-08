<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
?>
<div class="st-widget st-cart st-cart--spotlight" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <span class="st-cart-spotlight-label"><?php esc_html_e( 'Active Carts', 'bk-signals-for-woocommerce' ); ?></span>
    <strong><span class="st-stat-count" data-stat="active_cart"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span></strong>
    <span><?php esc_html_e( 'right now', 'bk-signals-for-woocommerce' ); ?></span>
</div>
