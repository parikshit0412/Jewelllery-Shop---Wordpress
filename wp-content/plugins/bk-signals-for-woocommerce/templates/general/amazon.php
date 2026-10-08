<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<?php if ( empty( $general_metrics ) || ! array_filter( array_column( $general_metrics, 'show' ) ) ) { return; } ?>
<div class="st-widget st-general st-general--amazon" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <?php foreach ( $general_metrics as $metric_key => $metric ) : ?>
        <?php if ( empty( $metric['show'] ) ) : continue; endif; ?>
        <div class="st-amazon-row st-general-item--<?php echo esc_attr( $metric_key ); ?>">
            <span class="st-amazon-label"><?php echo esc_html( $metric['label'] ); ?></span>
            <span class="st-amazon-value<?php echo esc_attr( 'live' === $metric_key ? ' st-live-count' : '' ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                <?php echo esc_html( BKSignals_Helpers::format_number( (int) $metric['value'] ) ); ?>
            </span>
        </div>
    <?php endforeach; ?>
</div>
