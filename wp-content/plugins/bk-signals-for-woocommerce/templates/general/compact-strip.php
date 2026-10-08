<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<?php if ( empty( $general_metrics ) || ! array_filter( array_column( $general_metrics, 'show' ) ) ) { return; } ?>
<div class="st-widget st-general st-general--compact-strip" data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <?php foreach ( $general_metrics as $metric_key => $metric ) : ?>
        <?php if ( empty( $metric['show'] ) ) : continue; endif; ?>
        <span class="st-compact-stat st-general-item--<?php echo esc_attr( $metric_key ); ?>">
            <span class="st-general-icon" aria-hidden="true"></span>
            <strong class="<?php echo esc_attr( 'live' === $metric_key ? 'st-live-count' : '' ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                <?php echo esc_html( BKSignals_Helpers::format_number( (int) $metric['value'] ) ); ?>
            </strong>
            <small><?php echo esc_html( $metric['label'] ); ?></small>
        </span>
    <?php endforeach; ?>
</div>
