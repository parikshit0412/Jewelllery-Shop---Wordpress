<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Admin_Settings {

    public static function init() {
        add_action( 'admin_post_bksignals_save_settings',  [ __CLASS__, 'save' ] );
        add_action( 'admin_post_bksignals_run_cron_now',   [ __CLASS__, 'run_cron_now' ] );
        add_action( 'admin_post_bksignals_reset_all',      [ __CLASS__, 'reset_all' ] );
        add_action( 'admin_post_bksignals_reset_product',  [ __CLASS__, 'reset_product' ] );
    }

    public static function render() {
        $settings = [
            'cron_interval'        => get_option( 'bksignals_cron_interval', 'bksignals_15min' ),
            'custom_cron_value'    => get_option( 'bksignals_custom_cron_value', '2' ),
            'custom_cron_unit'     => get_option( 'bksignals_custom_cron_unit', 'hours' ),
            'data_retention_days'  => get_option( 'bksignals_data_retention_days', '90' ),
            'live_refresh_seconds' => get_option( 'bksignals_live_refresh_seconds', '30' ),
            'live_timeout_seconds' => get_option( 'bksignals_live_timeout_seconds', '180' ),
            'track_admins'         => get_option( 'bksignals_track_admins', '0' ),
            'delete_data_on_uninstall' => get_option( 'bksignals_delete_data_on_uninstall', '0' ),
        ];
        $queue_count = BKSignals_Database::count_queued_views();
        include BKSIGNALS_DIR . 'admin/views/settings.php';
    }

    public static function save() {
        check_admin_referer( 'bksignals_settings_save' );
        if ( ! current_user_can( 'manage_woocommerce' ) ) wp_die( 'Forbidden' );

        $new_interval = self::sanitize_cron_interval( sanitize_key( wp_unslash( $_POST['cron_interval'] ?? 'bksignals_15min' ) ) );
        $old_interval = get_option( 'bksignals_cron_interval', 'bksignals_15min' );
        $old_custom_value = get_option( 'bksignals_custom_cron_value', '2' );
        $old_custom_unit = get_option( 'bksignals_custom_cron_unit', 'hours' );
        $custom_unit_raw = sanitize_key( wp_unslash( $_POST['custom_cron_unit'] ?? 'hours' ) );
        $custom_value = self::sanitize_custom_value( absint( wp_unslash( $_POST['custom_cron_value'] ?? 2 ) ), $custom_unit_raw );
        $custom_unit = self::sanitize_custom_unit( $custom_unit_raw );

        update_option( 'bksignals_cron_interval', $new_interval );
        update_option( 'bksignals_custom_cron_value', $custom_value );
        update_option( 'bksignals_custom_cron_unit', $custom_unit );

        $live_refresh_seconds = max( 5, min( 300, absint( wp_unslash( $_POST['live_refresh_seconds'] ?? 30 ) ) ) );
        $live_timeout_seconds = max( 30, min( 3600, absint( wp_unslash( $_POST['live_timeout_seconds'] ?? 180 ) ) ) );
        $live_timeout_seconds = max( $live_timeout_seconds, min( 3600, $live_refresh_seconds * 2 ) );

        update_option( 'bksignals_data_retention_days', absint( wp_unslash( $_POST['data_retention_days'] ?? 90 ) ) );
        update_option( 'bksignals_live_refresh_seconds', $live_refresh_seconds );
        update_option( 'bksignals_live_timeout_seconds', $live_timeout_seconds );
        update_option( 'bksignals_track_admins',        empty( $_POST['track_admins'] ) ? '0' : '1' );
        update_option( 'bksignals_delete_data_on_uninstall', empty( $_POST['delete_data_on_uninstall'] ) ? '0' : '1' );

        if (
            $new_interval !== $old_interval ||
            ( 'bksignals_custom' === $new_interval && ( (string) $custom_value !== (string) $old_custom_value || $custom_unit !== $old_custom_unit ) ) ||
            ! wp_next_scheduled( BKSignals_Cron::HOOK_FLUSH )
        ) {
            BKSignals_Cron::reschedule_flush( $new_interval );
        }

        wp_safe_redirect( BKSignals_Helpers::admin_notice_url( 'bk-signals-settings', 'saved' ) );
        exit;
    }

    public static function run_cron_now() {
        check_admin_referer( 'bksignals_run_cron_now' );
        if ( ! current_user_can( 'manage_woocommerce' ) ) wp_die( 'Forbidden' );

        BKSignals_Cron::run_all_now();

        wp_safe_redirect( BKSignals_Helpers::admin_notice_url( 'bk-signals-settings', 'cron_ran' ) );
        exit;
    }

    public static function reset_all() {
        check_admin_referer( 'bksignals_reset_all' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Forbidden' );

        BKSignals_Database::reset_all_stats();

        wp_safe_redirect( BKSignals_Helpers::admin_notice_url( 'bk-signals-settings', 'reset' ) );
        exit;
    }

    public static function reset_product() {
        check_admin_referer( 'bksignals_reset_product' );
        if ( ! current_user_can( 'manage_woocommerce' ) ) wp_die( 'Forbidden' );

        $pid = absint( wp_unslash( $_POST['product_id'] ?? 0 ) );
        if ( $pid ) {
            BKSignals_Database::reset_product_stats( $pid );
        }

        wp_safe_redirect( BKSignals_Helpers::admin_notice_url( 'bk-signals-settings', 'product_reset' ) );
        exit;
    }

    private static function sanitize_custom_unit( $unit ): string {
        $unit = sanitize_key( $unit );
        return in_array( $unit, [ 'minutes', 'hours', 'days' ], true ) ? $unit : 'hours';
    }

    private static function sanitize_cron_interval( $interval ): string {
        $interval = sanitize_key( $interval );
        return in_array( $interval, [ 'bksignals_15min', 'bksignals_30min', 'hourly', 'daily', 'bksignals_custom' ], true ) ? $interval : 'bksignals_15min';
    }

    private static function sanitize_custom_value( $value, $unit ): int {
        $unit = self::sanitize_custom_unit( $unit );
        $value = absint( $value );

        if ( 'days' === $unit ) {
            return max( 1, min( 30, $value ) );
        }

        if ( 'hours' === $unit ) {
            return max( 1, min( 168, $value ) );
        }

        return max( 5, min( 1440, $value ) );
    }
}
