<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
/* translators: %d: selected time window in days. */
$st_window_label = isset( $window_label ) ? $window_label : sprintf( __( 'the last %d days', 'bk-signals-for-woocommerce' ), (int) $window );
?>
<div class="st-widget st-sales st-sales--spotlight" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-window="<?php echo (int) $window; ?>">
    <span class="st-sales-spotlight-label"><?php esc_html_e( 'Sales', 'bk-signals-for-woocommerce' ); ?></span>
    <strong><span class="st-stat-count" data-stat="sales"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span></strong>
    <span><?php echo esc_html( $st_window_label ); ?></span>
</div>
