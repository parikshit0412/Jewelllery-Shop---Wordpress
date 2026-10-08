<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit;
/* translators: %d: selected time window in days. */
$st_window_label = isset( $window_label ) ? $window_label : sprintf( __( 'the last %d days', 'bk-signals-for-woocommerce' ), (int) $window );
?>
<div class="st-widget st-views st-views--clean" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-window="<?php echo (int) $window; ?>">
    <strong><span class="st-stat-count" data-stat="views"><?php echo esc_html( BKSignals_Helpers::format_number( $count ) ); ?></span></strong>
    <?php /* translators: %s: selected time window label. */ ?>
    <span class="st-clean-label"><?php printf( esc_html__( 'views in %s', 'bk-signals-for-woocommerce' ), esc_html( $st_window_label ) ); ?></span>
</div>
