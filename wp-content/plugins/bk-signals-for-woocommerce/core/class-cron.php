<?php
defined( 'ABSPATH' ) || exit;

class BKSignals_Cron {

    const HOOK_FLUSH    = 'bksignals_cron_flush';
    const HOOK_RECALC   = 'bksignals_cron_recalculate';
    const HOOK_CLEANUP  = 'bksignals_cron_cleanup';
    const HOOK_RETENTION = 'bksignals_cron_retention';

    public static function init() {
        self::register_intervals();

        add_action( self::HOOK_FLUSH,     [ __CLASS__, 'run_flush' ] );
        add_action( self::HOOK_RECALC,    [ __CLASS__, 'run_recalculate' ] );
        add_action( self::HOOK_CLEANUP,   [ __CLASS__, 'run_cleanup' ] );
        add_action( self::HOOK_RETENTION, [ __CLASS__, 'run_retention' ] );

        self::schedule();
    }

    public static function register_intervals() {
        if ( ! has_filter( 'cron_schedules', [ __CLASS__, 'add_intervals' ] ) ) {
            add_filter( 'cron_schedules', [ __CLASS__, 'add_intervals' ] );
        }
    }

    public static function add_intervals( $schedules ) {
        $schedules['bksignals_15min'] = [
            'interval' => 15 * MINUTE_IN_SECONDS,
            'display'  => __( 'Every 15 minutes (BK Signals)', 'bk-signals-for-woocommerce' ),
        ];
        $schedules['bksignals_30min'] = [
            'interval' => 30 * MINUTE_IN_SECONDS,
            'display'  => __( 'Every 30 minutes (BK Signals)', 'bk-signals-for-woocommerce' ),
        ];
        $schedules['bksignals_custom'] = [
            'interval' => self::custom_interval_seconds(),
            'display'  => self::custom_interval_label(),
        ];
        return $schedules;
    }

    public static function schedule() {
        self::register_intervals();

        $interval = self::normalize_interval( get_option( 'bksignals_cron_interval', 'bksignals_15min' ) );

        if ( ! wp_next_scheduled( self::HOOK_FLUSH ) ) {
            wp_schedule_event( time(), $interval, self::HOOK_FLUSH );
        }
        if ( ! wp_next_scheduled( self::HOOK_RECALC ) ) {
            wp_schedule_event( time() + 60, 'hourly', self::HOOK_RECALC );
        }
        if ( ! wp_next_scheduled( self::HOOK_CLEANUP ) ) {
            wp_schedule_event( time() + 120, 'hourly', self::HOOK_CLEANUP );
        }
        if ( ! wp_next_scheduled( self::HOOK_RETENTION ) ) {
            wp_schedule_event( time() + 180, 'daily', self::HOOK_RETENTION );
        }
    }

    public static function unschedule() {
        foreach ( [ self::HOOK_FLUSH, self::HOOK_RECALC, self::HOOK_CLEANUP, self::HOOK_RETENTION ] as $hook ) {
            wp_clear_scheduled_hook( $hook );
        }
    }

    public static function reschedule_flush( string $interval ) {
        self::register_intervals();
        wp_clear_scheduled_hook( self::HOOK_FLUSH );
        wp_schedule_event( time(), self::normalize_interval( $interval ), self::HOOK_FLUSH );
    }

    private static function normalize_interval( string $interval ): string {
        $allowed = [ 'bksignals_15min', 'bksignals_30min', 'hourly', 'daily', 'bksignals_custom' ];
        return in_array( $interval, $allowed, true ) ? $interval : 'bksignals_15min';
    }

    public static function custom_interval_seconds(): int {
        $value = absint( get_option( 'bksignals_custom_cron_value', 2 ) );
        $unit  = sanitize_key( get_option( 'bksignals_custom_cron_unit', 'hours' ) );

        if ( 'days' === $unit ) {
            return max( 1, min( 30, $value ) ) * DAY_IN_SECONDS;
        }

        if ( 'hours' === $unit ) {
            return max( 1, min( 168, $value ) ) * HOUR_IN_SECONDS;
        }

        return max( 5, min( 1440, $value ) ) * MINUTE_IN_SECONDS;
    }

    private static function custom_interval_label(): string {
        $value = absint( get_option( 'bksignals_custom_cron_value', 2 ) );
        $unit  = sanitize_key( get_option( 'bksignals_custom_cron_unit', 'hours' ) );
        $value = max( 1, $value );

        if ( 'days' === $unit ) {
            /* translators: %d: cron interval in days. */
            return sprintf( __( 'Every %d day(s) (BK Signals Custom)', 'bk-signals-for-woocommerce' ), min( 30, $value ) );
        }

        if ( 'hours' === $unit ) {
            /* translators: %d: cron interval in hours. */
            return sprintf( __( 'Every %d hour(s) (BK Signals Custom)', 'bk-signals-for-woocommerce' ), min( 168, $value ) );
        }

        /* translators: %d: cron interval in minutes. */
        return sprintf( __( 'Every %d minute(s) (BK Signals Custom)', 'bk-signals-for-woocommerce' ), max( 5, min( 1440, $value ) ) );
    }

    public static function run_flush() {
        BKSignals_Database::flush_view_queue();
    }

    public static function run_recalculate() {
        BKSignals_Database::recalculate_rolling_windows();
    }

    public static function run_cleanup() {
        BKSignals_Database::cleanup_live_visitors();
        BKSignals_Database::cleanup_active_carts();
        BKSignals_Database::cleanup_invalid_order_sales();
    }

    public static function run_retention() {
        BKSignals_Database::purge_old_daily_stats();
    }

    public static function run_all_now() {
        self::run_flush();
        self::run_recalculate();
        self::run_cleanup();
    }
}
