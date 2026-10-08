<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
?>
<div class="people-add text-primary st-widget st-cart st-cart--minimal" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                                            <i class="icon icon-shopping-cart-simple"></i>
                                            <span class="st-stat-count" data-stat="active_cart"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span>
    <?php esc_html_e( 'people just added this product to their cart', 'bk-signals-for-woocommerce' ); ?>
</div>
