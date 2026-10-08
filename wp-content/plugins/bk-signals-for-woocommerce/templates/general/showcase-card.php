<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<?php if ( empty( $general_metrics ) || ! array_filter( array_column( $general_metrics, 'show' ) ) ) { return; } ?>
<div class="st-widget st-general st-general--showcase-card" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <div class="st-showcase-header">
        <span>
            <?php
            /* translators: %s: selected time window label. */
            echo esc_html( sprintf( __( 'Activity in %s', 'bk-signals-for-woocommerce' ), $window_label ) );
            ?>
        </span>
    </div>
    <div class="st-showcase-grid">
        <?php foreach ( $general_metrics as $metric_key => $metric ) : ?>
            <?php if ( empty( $metric['show'] ) ) : continue; endif; ?>
            <div class="st-showcase-item st-general-item--<?php echo esc_attr( $metric_key ); ?>">
                <div class="st-showcase-icon" aria-hidden="true"></div>
                <div class="st-showcase-value<?php echo esc_attr( 'live' === $metric_key ? ' st-live-count' : '' ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                    <?php echo esc_html( BKSignals_Helpers::format_number( (int) $metric['value'] ) ); ?>
                </div>
                <div class="st-showcase-label"><?php echo esc_html( $metric['label'] ); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
