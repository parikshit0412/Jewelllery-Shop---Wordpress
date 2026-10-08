<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'ABSPATH' ) || exit; ?>
<?php
$design = sanitize_html_class( $a['design'] ?? 'trend-alert' );
$messages = [];
$ticker_designs = [ 'trend-alert', 'market-signal', 'orange-pulse', 'trust-line', 'live-feed' ];
$is_ticker = in_array( $design, $ticker_designs, true );

if ( ! empty( $general_metrics['views']['show'] ) ) {
    $messages[] = [
        'prefix' => __( 'Popular product!', 'bk-signals-for-woocommerce' ),
        'value'  => BKSignals_Helpers::format_number( (int) $general_metrics['views']['value'] ),
        'suffix' => sprintf(
            /* translators: %s: selected time window label. */
            __( 'people viewed this in %s.', 'bk-signals-for-woocommerce' ),
            $window_label
        ),
        'static_suffix' => __( 'people viewed this product.', 'bk-signals-for-woocommerce' ),
        'label'  => __( 'views', 'bk-signals-for-woocommerce' ),
        'type'   => 'views',
        'live'   => false,
    ];
}

if ( ! empty( $general_metrics['cart']['show'] ) ) {
    $messages[] = [
        'prefix' => __( 'Demand is rising:', 'bk-signals-for-woocommerce' ),
        'value'  => BKSignals_Helpers::format_number( (int) $general_metrics['cart']['value'] ),
        'suffix' => __( 'people have this in cart.', 'bk-signals-for-woocommerce' ),
        'static_suffix' => __( 'people have this in cart.', 'bk-signals-for-woocommerce' ),
        'label'  => __( 'in carts', 'bk-signals-for-woocommerce' ),
        'type'   => 'cart',
        'live'   => false,
    ];
}

if ( ! empty( $general_metrics['sales']['show'] ) ) {
    $messages[] = [
        'prefix' => __( 'Trusted by shoppers:', 'bk-signals-for-woocommerce' ),
        'value'  => BKSignals_Helpers::format_number( (int) $general_metrics['sales']['value'] ),
        'suffix' => sprintf(
            /* translators: %s: selected time window label. */
            __( 'purchases in %s.', 'bk-signals-for-woocommerce' ),
            $window_label
        ),
        'static_suffix' => __( 'purchases recorded.', 'bk-signals-for-woocommerce' ),
        'label'  => __( 'sales', 'bk-signals-for-woocommerce' ),
        'type'   => 'sales',
        'live'   => false,
    ];
}

if ( ! empty( $general_metrics['live']['show'] ) ) {
    $messages[] = [
        'prefix' => __( 'Live now:', 'bk-signals-for-woocommerce' ),
        'value'  => (int) $general_metrics['live']['value'],
        'suffix' => __( 'people are viewing this product.', 'bk-signals-for-woocommerce' ),
        'static_suffix' => __( 'people are viewing this product.', 'bk-signals-for-woocommerce' ),
        'label'  => __( 'live', 'bk-signals-for-woocommerce' ),
        'type'   => 'live',
        'live'   => true,
    ];
}

if ( empty( $messages ) ) {
    return;
}

$message_count = count( $messages );
?>
<?php if ( $is_ticker ) : ?>
    <div class="st-widget st-general st-general-ticker st-general-ticker--<?php echo esc_attr( $design ); ?> st-general-ticker--count-<?php echo (int) $message_count; ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
        <span class="st-general-ticker-track">
            <?php foreach ( $messages as $index => $message ) : ?>
                <span class="st-general-ticker-message">
                    <span class="st-general-message-icon st-general-message-icon--<?php echo esc_attr( $message['type'] ?? 'views' ); ?>" aria-hidden="true"></span>
                    <span class="st-general-ticker-prefix"><?php echo esc_html( $message['prefix'] ); ?></span>
                    <strong class="<?php echo esc_attr( ! empty( $message['live'] ) ? 'st-live-count' : '' ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>"><?php echo esc_html( $message['value'] ); ?></strong>
                    <span class="st-general-ticker-suffix"><?php echo esc_html( $message['suffix'] ); ?></span>
                </span>
            <?php endforeach; ?>
        </span>
    </div>
<?php else : ?>
    <?php $main_message = $messages[0]; ?>
    <div class="st-widget st-general st-general-static st-general-static--<?php echo esc_attr( $design ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
        <div class="st-general-static-head">
            <span class="st-general-static-window"><?php echo esc_html( $window_title_label ?? ucwords( $window_label ) ); ?></span>
        </div>
        <div class="st-general-static-main">
            <span><?php echo esc_html( $main_message['prefix'] ); ?></span>
            <strong class="<?php echo esc_attr( ! empty( $main_message['live'] ) ? 'st-live-count' : '' ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>"><?php echo esc_html( $main_message['value'] ); ?></strong>
            <span><?php echo esc_html( $main_message['static_suffix'] ?? $main_message['suffix'] ); ?></span>
        </div>
        <div class="st-general-static-pills">
            <?php foreach ( $messages as $message ) : ?>
                <span>
                    <strong class="<?php echo esc_attr( ! empty( $message['live'] ) ? 'st-live-count' : '' ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>"><?php echo esc_html( $message['value'] ); ?></strong>
                    <?php echo esc_html( $message['label'] ?? $message['prefix'] ); ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
