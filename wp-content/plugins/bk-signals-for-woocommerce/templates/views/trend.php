<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
/* translators: %d: selected time window in days. */
$st_window_label = isset( $window_label ) ? $window_label : sprintf( __( 'the last %d days', 'bk-signals-for-woocommerce' ), (int) $window );
?>
<div class="st-widget st-views st-views--trend" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-window="<?php echo (int) $window; ?>">
    <span class="st-trend-icon">
        <svg class="st-icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
    </span>
    <span class="st-trend-copy">
        <strong><span class="st-stat-count" data-stat="views"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span></strong>
        <?php /* translators: %s: selected time window label. */ ?>
        <small><?php printf( esc_html__( 'views in %s', 'bk-signals-for-woocommerce' ), esc_html( $st_window_label ) ); ?></small>
    </span>
</div>
