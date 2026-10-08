<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( '1' !== get_option( 'bksignals_delete_data_on_uninstall', '0' ) ) {
    return;
}

global $wpdb;

$tables = [
    $wpdb->prefix . 'bksignals_product_stats',
    $wpdb->prefix . 'bksignals_recent_sales',
    $wpdb->prefix . 'bksignals_sales_events',
    $wpdb->prefix . 'bksignals_live_visitors',
    $wpdb->prefix . 'bksignals_active_carts',
    $wpdb->prefix . 'bksignals_daily_stats',
    $wpdb->prefix . 'bksignals_view_queue',
];

foreach ( $tables as $table ) {
    $wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table ) ); // phpcs:ignore
}

$options = [
    'bksignals_db_version',
    'bksignals_live_index_version',
    'bksignals_daily_index_version',
    'bksignals_active_cart_table_version',
    'bksignals_sales_events_table_version',
    'bksignals_recent_sales_order_columns_version',
    'bksignals_sales_customer_columns_version',
    'bksignals_live_visitors_enabled',
    'bksignals_views_enabled',
    'bksignals_cart_enabled',
    'bksignals_sales_enabled',
    'bksignals_popup_enabled',
    'bksignals_popup_delay',
    'bksignals_popup_duration',
    'bksignals_popup_interval',
    'bksignals_popup_limit',
    'bksignals_popup_product_scope',
    'bksignals_popup_mobile_enabled',
    'bksignals_popup_mobile_offset',
    'bksignals_popup_mobile_position',
    'bksignals_popup_design',
    'bksignals_popup_position',
    'bksignals_live_threshold',
    'bksignals_view_window',
    'bksignals_live_refresh_seconds',
    'bksignals_live_timeout_seconds',
    'bksignals_active_cart_timeout_days',
    'bksignals_cron_interval',
    'bksignals_custom_cron_value',
    'bksignals_custom_cron_unit',
    'bksignals_live_visitors_widgets',
    'bksignals_views_widgets',
    'bksignals_cart_widgets',
    'bksignals_sales_widgets',
    'bksignals_general_widgets',
    'bksignals_delete_data_on_uninstall',
];

foreach ( $options as $option ) {
    delete_option( $option );
}
