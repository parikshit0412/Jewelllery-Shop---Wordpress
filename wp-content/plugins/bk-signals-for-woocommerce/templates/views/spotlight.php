<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
/* translators: %d: selected time window in days. */
$st_window_label = isset( $window_label ) ? $window_label : sprintf( __( 'the last %d days', 'bk-signals-for-woocommerce' ), (int) $window );
?>
<div class="st-widget st-views st-views--spotlight" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-window="<?php echo (int) $window; ?>">
    <div class="st-spotlight-header">
        <svg class="st-icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s4-8 10-8 10 8 10 8-4 8-10 8-10-8-10-8z"/><circle cx="12" cy="12" r="3"/></svg>
        <span class="st-spotlight-label"><?php esc_html_e( 'Views', 'bk-signals-for-woocommerce' ); ?></span>
    </div>
    <strong><span class="st-stat-count" data-stat="views"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span></strong>
    <span class="st-spotlight-period"><?php echo esc_html( $st_window_label ); ?></span>
</div>
