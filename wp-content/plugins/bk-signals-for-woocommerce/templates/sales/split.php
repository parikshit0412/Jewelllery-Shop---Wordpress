<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
/* translators: %d: selected time window in days. */
$st_window_label = isset( $window_label ) ? $window_label : sprintf( __( 'the last %d days', 'bk-signals-for-woocommerce' ), (int) $window );
?>
<div class="st-widget st-sales st-sales--split" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-window="<?php echo (int) $window; ?>">
    <div class="st-sales-split-count"><span class="st-stat-count" data-stat="sales"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span></div>
    <div class="st-sales-split-info">
        <svg class="st-sales-icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73L13 2.27a2 2 0 0 0-2 0L4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7 12 12l8.7-5"/><path d="M12 22V12"/></svg>
        <span><?php esc_html_e( 'Sales', 'bk-signals-for-woocommerce' ); ?></span>
        <small><?php echo esc_html( $st_window_label ); ?></small>
    </div>
</div>
